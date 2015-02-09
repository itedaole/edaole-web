<?php
//用户相关

define('IS_LOGIN','api_islogin');
define('USER_ID','user_id');//user_id
define('MOBILE_VERIFY','MOBILE_VERIFY');

define('TAB_USER','user');

class UserEngine extends BaseEngine{
	static public $cookie_name = 'ru';

	static function userId(){
		return $_SESSION[USER_ID];	
	}

	function userTab(){
		return M(TAB_USER);
	}

	function register($name,$vcode,$email,$pwd){
		// var_dump(func_get_args());
		if($this->checkUserExist("username",$name)){
			$this->error=C('ERR_4021');
			return false;
		}
		if($this->checkUserExist("email",$email)){
			$this->error=C('ERR_4022');
			return false;
		}
// 		$verity=$_SESSION[MOBILE_VERIFY];
// 		if ($verity['code']!=$vcode) {
// 			$this->error=C('ERR_4023');
// 			return false;
// 		}

		$data['username']=$name;
		$data['password']=Utils::UserPassword($pwd);
		$data['email']=$email;
		$data['mobile']=Utils::getVerifyMobile();
		$data['create_time']=time();//创建时间
		$db=self::userTab();
		Utils::destroyMobileVerifyCode();
		if($db->add($data))return true;
		$this->error=C('ERR_500');
		return false;
	}

	static function checkUserExist($key,$value){
		$map[$key]=array('EQ',$value);
		if(self::objFromTable(TAB_USER,$map))return true;
		return false;
	}

	function login($name,$pwd){
		// var_dump(func_get_args());
		if (!$name||!$pwd) {
			$this->error=C('ERR_400');
			return false;
		}
		//email
		if (strstr($name,'@')) {
			$map['email']=array('EQ',$name);
		}else{//name
			$map['username']=array('EQ',$name);
		}
		$map['password']=array('EQ',Utils::UserPassword($pwd));
		$res=self::objFromTable(TAB_USER,$map);
		if ($res) {
			self::remenberLogin($res);
			$_SESSION[IS_LOGIN]=true;
			$_SESSION['user_id']=intval($res['id']);
			$res=Utils::smartSimplifyArr($res,'password,create_time,login_time,secret');
			$config['requireMobile']=isYES(Utils::SystemConfig('option.bindmobile'))?'1':'0';
			$res['_config']=$config;
			return $res;
		}else{
			$this->error=C('ERR_401');
			// $this->error['sql']=$db->getLastSql();
			return false;
		}
	}

	static public function remenberLogin($user){
		$zone = "{$user['id']}@{$user['password']}";
		Utils::cookieset(self::$cookie_name, base64_encode($zone), 300*86400);
	}

	static public function GetLoginCookie($cname='ru') {
		$cv = Utils::cookieget($cname);
		if ($cv) {
			$zone = base64_decode($cv);
			$p = explode('@', $zone, 2);
			$map['id']=array('EQ',$p[0]);
			$map['password']=$p[1];
			$res=self::objFromTable(TAB_USER,$map);
			if ($res){
				self::remenberLogin($res);
				$_SESSION[IS_LOGIN]=true;
				$_SESSION['user_id']=intval($res['id']);
				$res=Utils::smartSimplifyArr($res,'password,create_time,login_time,secret');
				return $res;
			}
			return false;
		}
		return false;
	}

	function logout(){
		unset($_SESSION[IS_LOGIN]);
		unset($_SESSION['user_id']);
		Utils::cookieset(self::$cookie_name, null, -1);	
	}

	function isLogin(){
		if ($_SESSION['user_id']) {
			return $_SESSION['user_id'];
		}else{
			return self::GetLoginCookie(self::$cookie_name);
		}
	}

	static function userCoupon($type,$fillTeam=false){
		$uid=self::userId();
		if ($uid) {
			$map=array();
			switch ($type) {
				case 1:
					$map['consume']=array('EQ','Y');
					break;
				case 2:
					$map['consume']=array('EQ','N');
					$map['expire_time']=array('ELT',time());
					break;
				case 3: //获取全部
					// $map[expire_time]=array('ELT',time());
					break;
				default:
					$map['consume']=array('EQ','N');
					$map['expire_time']=array('EGT',time());
					break;
			}
			$map['user_id']=$uid;
			$resArr=self::objsFromTable('coupon',$map,false,'create_time DESC');
			if($fillTeam)TuanEngine::fillTuanInfo($resArr);
			return $resArr?$resArr:array();
		}else{
			return false;
		}
	}

	/*
	查询银联未完成支付订单
	塞在这里好像太不合适了···
	*/
	static function queryUMSOrder(){
		$ums = Utils::buildUmspay();
		if ($ums) {//如果支持银联
			$uid=self::userId();
			$map['user_id']=array('EQ',$uid);
			$map['state']=array('EQ','unpay');
			$resArr=self::objsFromTable('order',$map);
			foreach ($resArr as $value) {
				$key = 'ums_'.$value['pay_id'];
				$data=S($key);
				if (!$data||!$value['pay_id']){
					continue;
				}
				
				S($key,NULL);
				$cont=$ums->queryOrder($data['orderId'],$data['TransId']);
				if (!$ums->orderIsPay($cont)) {//如果订单没没支付
					continue;
				}
				// pr($cont);
				$cont['MerOrderId']=$data['orderId'];
				$cont['TransId']=$data['TransId'];
				$cont['TransAmt']=1;

				if ($cont) {
					$v_oid=$cont['MerOrderId'];
					list($_, $id, $_, $_) = explode('-', $v_oid, 4);

					$trade_no=$cont['TransId'];
					$money=$cont['TransAmt'];
					$money=Utils::moneyit($money*0.01);
					$currency = 'CNY';
					$service = 'umspay';
					$bank = '银联全民捷付';
					$res=Utils::teamOtherPay($id, $v_oid, $money, $currency, $service, $bank,$trade_no);
				}
			}
		}
	}


	static function userOrders($orderId=NULL,$teamId=NULL,$fillTeam=false,&$count=NULL,&$page=NULL){
		$uid=self::userId();
		if ($uid) {
			$map['user_id']=array('EQ',$uid);
			if ($orderId)$map['id']=array('EQ',$orderId);
			if ($teamId)$map['team_id']=array('EQ',$teamId);
			$resArr=self::objsFromTable('order',$map,false,NULL,$count,$page);
			if($resArr){
				if ($fillTeam) {
					TuanEngine::fillTuanInfo($resArr);
				}
				foreach ($resArr as &$item) {
					$tuan = $item['team'];
					$filter='detail,systemreview,'.C('TeamFieldFilter');
					$tuan=Utils::smartSimplifyArr($tuan,$filter);
					Utils::htmlspecialchars($tuan,'notice,summary');	
					$item['team']=$tuan;
				}
			}
			return $resArr?$resArr:array();
		}else{
			return false;
		}
	}

	static function userOrderById($id,$fillTeam=false){
		if ($id) {
			$res=self::userOrders($id,NULL,$fillTeam);
			return $res[0];
		}else{
			return false;
		}
		
	}


	function userInfo($id)
	{
		if (!$id) $id=self::userId();
		$res=self::objById(TAB_USER,$id);
		$res=Utils::smartSimplifyArr($res,'password,create_time,login_time,secret');
		return $res;
	}



//msm 验证码
	function mobileVerify($number){
		$ipKey='vkey_'.$number;
		if (!APP_DEBUG) {
			$config=Utils::SystemConfig('sms');
			if (S($ipKey)>$config['numbers']) {
				return '此号码短信验证码获取次数过多';
			}else{
				S($ipKey,1+S($ipKey),C('SMS_VERIFY_EXP'));
			}
		}
		if (self::checkUserExist('mobile',$number)) {
			return "该手机号已绑定过用户";
		}
		$msmCode=rand(C('MobileVerifyCodeRangeMin'),C('MobileVerifyCodeRangeMax'));
		$text=C('MobileVerifyMsg');
		$text=str_replace('MSMCode', $msmCode, $text);
		$res=Utils::smsSend($number,$text);
		if ($res['result']===true) {
			$_SESSION[MOBILE_VERIFY]=array('tell'=>$number,'code'=>$msmCode);
			return true;
		}else{
			Utils::destroyMobileVerifyCode();
			return '验证码发送失败'.(is_array($res)?json_encode($res):$res);
		}
	}

	function updateMobile($uid,$tell){
		$user=self::userTab();
		if ($uid&&$tell) {
			$data['mobile'] = $tell;
			return $user->where('id=%d',$uid)->save($data);
		}
		return false;
	}
}