<?php
// 团购相关
define(TAB_TEAM, 'team');

class TuanEngine extends BaseEngine{

	function getTenPayTokenUrl($order_id,$sale_plat){
		$user=new UserEngine();
		if (!$sale_plat)$sale_plat='211';
		$userOrder=$user->userOrderById($order_id,true);
		if (!$userOrder) {
			return;
		}
		$team=$userOrder['team'];

		$total_fee=$userOrder['origin']-$userOrder['credit'];
		
		$url=TenpayFuns::intiTenpayUrl($userOrder['pay_id'],$team['product'],$total_fee,$sale_plat,$userOrder['id']);
		return $url;
	}

	function getAlipayStr($order_id){
		$user=new UserEngine();
		$userOrder=$user->userOrderById($order_id,true);
		if (!$userOrder) {
			return;
		}
		$team=$userOrder['team'];

		$total_fee=$userOrder['origin']-$userOrder['credit'];
		
		$res=AlipayFuns::initPayStr($userOrder['pay_id'],$team['title'],$team['product'],$total_fee);
		return $res;
	}

	function getUmspayStr($order_id){
		$ums=Utils::buildUmspay();
		if (!$ums) {
			return false;
		}

		$user=new UserEngine();
		$userOrder=$user->userOrderById($order_id,true);
		if (!$userOrder) {
			return;
		}
		$team=$userOrder['team'];
		$total_fee=$userOrder['origin']-$userOrder['credit'];

		$cont=$ums->order($team['product'],$total_fee,$userOrder['pay_id']);
		// var_dump($cont);
		if ($cont) {
			//缓存如银联的订单数据，用于订单查询，数据库没地方放，迫于无奈放这里
			$key = 'ums_'.$cont['MerOrderId'];
			$data['TransId']=$cont['TransId'];
			$data['orderId']=$cont['MerOrderId'];
			S($key,$data,array('expire' => 3600));

			if (isIOS()) {
				$content='transId='.$cont['TransId'];
				$content.='&chrCode='.$cont['ChrCode'];
				$content.='&cerSign='.$cont['sigin'];
				$content.='&merchantId='.$cont['MerOrderId'];
				$content.='&isTest='.$cont['isTest'];
			}else{
				$content='';
				$content.=$cont['sigin'];
				$content.='|';
				$content.=$cont['ChrCode'];
				$content.='|';
				$content.=$cont['TransId'];
				$content.='|';
				$content.=$cont['MerOrderId']; //android
			}
			return $content;
		}
		return false;

	}
	function getWxPayStr($order_id){
		if (C('wechatPayPartner')&&C('wechatPaySignKey')) {
			$user=new UserEngine();

			$userOrder=$user->userOrderById($order_id,true);
			if (!$userOrder) {
				return;
			}
			$team=$userOrder['team'];

			$total_fee=$userOrder['origin']-$userOrder['credit'];
			
			$total_fee*=100;

			$data['AppId']=C('wechatAppID');
			$data['PartnerId']=C('wechatPayPartner');
			$data['SignKey']=C('wechatPaySignKey');
			$data['AppSecret']=C('wechatPayAppSecret');
    

			$data['pakage']=WeChatPayFuns::creatPackage($team['product'],$userOrder['pay_id'],$total_fee);
			return json_encode($data);
		}
		return false;
	}

	function buy($sid,$uid,$num,$name,$address,$zipcode,$remark,$condbuy,$expressid,$from=null){
		$res=$this->goodById($sid);
		if (!$res) {
			$this->error=C('ERR_400');
			$this->error['text']='本商品不存在';
			return false;
		}
		
		if ($res['end_time']<time()) {
			$this->error=C('ERR_402');
			$this->error['text']='本商品已经过期';
			return false;
		}

		$user=new UserEngine();
		$userOrder=$user->userOrders(NULL,$sid,false);
		// buyonce

		$count=0;
		foreach ($userOrder as $key => $value) {
			if ($value['state']=='pay') {
				@list($_, $_, $n, $_) = explode('-', $value['pay_id'], 4);
				$count+=$n;
			}
		}
		if ($res['buyonce']=='Y'&&$count>0) {
			$this->error=C('ERR_400');
			$this->error['text']='您已经成功购买了本单产品，请勿重复购买，快去关注一下其他产品吧！';
			return false;
		}
		if ($res['per_number']>0&&$count+$num>$res['per_number']) {
			$this->error=C('ERR_400');
			$this->error['text']='本商品每人限购:'.$res['per_number']."件";
			return false;
		}
		if ($res['permin_number']>0&&$num<$res['permin_number']) {
			$this->error=C('ERR_400');
			$this->error['text']='本商品最低购买:'.$res['permin_number']."件";
			return false;
		}
		if ($res['max_number']>0&&$res['now_number']+$num>$res['max_number']) {
			$this->error=C('ERR_400');
			$this->error['text']="本商品不足,无法购买";
			return false;
		}

		if ($res['id']>0) {
			$userInfo=$user->userInfo($uid);

			$db=M('order');
			$data['user_id']=$uid;
			$data['allowrefund']=$res['allowrefund'];//是否可以退款
			$data['quantity']=$num;
			$data['team_id']=$res['id'];
			$data['city_id']=$res['city_id'];
			$data['partner_id']=$res['partner_id']?$res['partner_id']:0;

			$data['price']=$res['team_price'];
			$data['origin']=$data['price']*$num;//商品价格
			//计算运费
			if ($res['delivery']=='express') {
				if ($res['farefree']==0||$res['farefree']>$num) {
				$express=Utils::objInArr($res['express_relate'],'id',$expressid);
				if (!$express)$express=$res['express_relate'][0];
				$data['origin']+=$express['price'];
				}
			}
			
			if ($userInfo['money']>=$data['origin']) {
				$data['state']='unpay';
				$data['service']='credit';
			}else{
				$data['credit']=$userInfo['money'];
				$data['service']='tenpay';
				$data['state']='unpay';
			}

			$data['mobile']=$userInfo['mobile'];
			$data['condbuy']=$condbuy;
		
			if ($res['delivery']=='express') {//快递团购
				$data['express']='Y';
				$data['express_id']=$expressid?$expressid:0;
				$data['realname']=$name;
				$data['address']=$address;
				$data['zipcode']=$zipcode;
			}else{
				$data['express']='N';
				$data['express_id']='0';
				$data['realname']='';
				$data['address']='';
				$data['zipcode']='';
			}
			
			$data['remark']=$remark;

			$oldorder=NULL;
			foreach ($userOrder as $value) {
				if ($value['state']=='unpay'&&$value['rstate']=='normal') {
					$oldorder=$value;
					break;
				}
			}
			$index=$oldorder['id'];
			$randid = strtolower(Utils::ztGenSecret(4));
			if ($oldorder) {//更新已经有的订单	
				// echo "有订单";
				$pay_id = "go-".$index."-".$num."-".$randid;
				$data['pay_id']=$pay_id;
				$db->where('id=%d',$index)->save($data);
				Utils::recordOrder($uid,$index,$from);
			}else{//生成新订单
				// echo "生成新订单";
				$data['create_time']=time();//创建时间
				$index=$db->add($data);
				// echo $db->getLastSql();
				$pay_id = "go-".$index."-".$num."-".$randid;
				$newdata=array();
				$newdata['pay_id']=$pay_id;
				$db->where('id=%d',$index)->save($newdata);
				Utils::recordOrder($uid,$index,$from);
			}
			$ress = $db->where('id=%d',$index)->find();
			if ($ress) {
				$ress['money']=$userInfo['money']-$ress['origin'];
				$ress['money']=$ress['money']<0?0:$ress['money'];
				$host=Utils::host();
				if($ress['service']=='credit'){
					//余额支付
					$ress['credit']=$ress['origin'];
					$ress['pay_url']=$host.'/app/api.php?s=/Tuan/pay&source=_client&id='.$index;
				}else{
					$tokenUrl=$this->getTenPayTokenUrl($ress['id'],$from);
					if ($tokenUrl) {//财付通取token地址
						$ress['token_url']=$tokenUrl;
					}

					$alipayStr=$this->getAlipayStr($ress['id']);
					if ($alipayStr) {//如果有支付宝支付配置
						$ress['alipay_str']=$alipayStr;
					}

					$umspayStr=$this->getUmspayStr($ress['id']);
					if ($umspayStr) {//银联-全民付
						$ress['umspay_str']=$umspayStr;
					}
					$wxpay=$this->getWxPayStr($ress['id']);
					if ($wxpay) {
						$ress['wx_pay']=$wxpay;	
					}
				}
				$tenpay=Utils::getTenpay();
				if($tenpay)$ress['tenpayKey']=$tenpay['mid'];
				return Utils::simplifyArr($ress,array('buy_id','admin_id','city_id','card_id','trade_no','rereason','retime','card','pay_time','comment_content','comment_display','comment_grade','comment_wantmore','comment_time','sms_express','luky_id','adminremark'));
			}else{
				$this->error=C('ERR_500');
				return false;
			}
		}else{
			$this->error=C('ERR_500');
			return false;
		}
}




//获取团购类型
	function tuanTypeList(){
		$db=M('category');
		$map['zone']=array('EQ','group');
		$map['display']=array('EQ','Y');
		$data=$db->where($map)->field('id,name,fid')->order('sort_order DESC')->select();
		$res=array();
		foreach ($data as $key => $value) {
			if ($value['fid']=='0') {
				if ($res[$value['id']]['subClasss']) {
					$subClass=$res[$value['id']]['subClasss'];
					$value['subClasss']=$subClass;
					$res[$value['id']]= $value;
				}else{
					$res[$value['id']]= $value;	
				}
			}else{
				$res[$value['fid']]['subClasss'][]=$value;	
			}
		}
		$res=array_values($res);
		return $res===NULL?array():$res;
	}

	function tuanCityList(){
		$db=M('category');
		$map['zone']=array('EQ','city');
		$map['display']=array('EQ','Y');
		$data=$db->where($map)->field('id,name')->select();
		return $data===NULL?array():$data;
	}

	static function teamsByIds($ids,$where=NULL,$maping=false){
		$teams=self::objsByIds(TAB_TEAM,$ids,$where,$maping);
		return Utils::smartSimplifyArr($teams,C('TeamFieldFilter'));
	}

	static function teamById($id){
		$team=self::teamsByIds($id);
		return $team[0];
	}

	// //$summary是否只获取摘要
	function goodsByIds($ids){
		$goods=self::teamsByIds($ids);
		PartnerEngine::fillPartnerInfo($goods);
		self::fillExpressInfo($goods);
		self::fillImagePath($goods);
		return $goods;
	}
	function goodById($id){
		$goods=self::goodsByIds($id);
		return $goods[0];
	}



//团购列表
	function goodsList($key,$type,$city_id,&$count,&$page,$orderby,$lng,$lat,$partnerid,$coord=NULL,$ignore=NULL){
		$where=array();
		$subwhere=array();
		$key=trim($key);
		if ($key) {
			if (C('SEARCH_WORD_LEAVE')) {
				$keys=explode(' ', $key);
				foreach ($keys as $key => &$value) {
					$value=trim($value);
					if (!$value) {
						unset($keys[$key]);
					}else{
						//防爆库过滤
						$value=str_replace("%","\%",$value);
						$value=str_replace("_","\_",$value);
						$value='%'.$value.'%';
					}
				}
			}else{
				$key=str_replace("%","\%",$key);
				$key=str_replace("_","\_",$key);
				$key='%'.$key.'%';
			}
    		if (C('SEARCH_SWITCH_TITLE'))
			$subwhere['title'] = array('LIKE',$keys?$keys:$key);
    		if (C('SEARCH_SWITCH_PRODUCT'))
			$subwhere['product'] = array('LIKE',$keys?$keys:$key);
    		if (C('SEARCH_SWITCH_SEOKEYWORD'))
			$subwhere['seo_keyword'] = array('LIKE',$keys?$keys:$key);
			if (C('SEARCH_OTHER_KEYS')!='') {
				$otherKeys=explode(',', C('SEARCH_OTHER_KEYS'));
				foreach ($otherKeys as $value) {
					$subwhere[$value] = array('LIKE',$keys?$keys:$key);	
				}
			}
			$subwhere['_logic'] = 'or';
			$where['_complex'] = $subwhere;
		}
		if ($city_id!=0) {
			$where['city_ids'] = array('LIKE','%@'.$city_id.'@%');
		}
		if ($type) {
			$type=explode('@', $type);
			if (count($type)>1) {
				if ($type[0])$where['group_id']=array('EQ',$type[0]);
				if ($type[1])$where['sub_id']=array('EQ',$type[1]);
			}else{
				if ($type[0])$where['group_id']=array('EQ',$type[0]);
			}
		}
		if ($partnerid) {
			$where['partner_id'] = array('EQ',intval($partnerid));
		}
		$time=time();
		$where['begin_time']=array('ELT',$time);
		$where['end_time']=array('EGT',$time);
		
		if ($ignore) {
			$where['id'] = array('NOT IN',$ignore);
		}

		if(C('TeamCondition')){//自定义筛选条件
			$where['_string'] = C('TeamCondition');
		}
		$orderby=Utils::transformOrderBy($orderby);//处理排序函数
		if(!$orderby){
			$orderby='sort_order DESC,id DESC';	
		}else{
			if (strpos($orderby,'id')===false)$orderby.=',id DESC';
		}
		if (str_prefix($orderby,'range')&&$lng!==NULL&&$lat!==NULL) {
			$where['partner_id']=array('NEQ',0);//过滤没有商家的团购，因为没有商家就没有位置数据，
			$res=self::objsFromTable(TAB_TEAM,$where);
			PartnerEngine::fillPartnerInfo($res,$lng,$lat,$coord);//填充商家数据
			foreach ($res as $index => $item) {
				if (!$item['partner']||!$item['partner']['longlat'])unset($res[$index]);
			}
			if (strpos($orderby, 'DESC')) {
				$res=Utils::array_sort($res,'_range','DESC');
			}else{
				$res=Utils::array_sort($res,'_range','asc');
			}
			$res=Utils::getListByPage($res,$count,$page);
		}else{
			$res=self::objsFromTable(TAB_TEAM,$where,false,$orderby,$count,$page);
			PartnerEngine::fillPartnerInfo($res,$lng,$lat,$coord);//填充商家数据
		}
		// var_dump($res);
		$filter='detail,systemreview,'.C('TeamFieldFilter');
		$res=Utils::smartSimplifyArr($res,$filter);
		Utils::htmlspecialchars($res,'notice,summary');
		foreach ($res as $key => &$value) {
			Utils::FillTuanSystemInfo($value);
		}
		self::fillExpressInfo($res);
		self::fillImagePath($res);
		return $res;
	}

	static function fillImagePath(&$tuan){
		if (is_array($tuan)&&$tuan) {
			$prefix=Utils::TuanImagePrefix();
			$LSuffix=C('TuanLargeImageSuffix');
			$SSuffix=C('TuanSmallImageSuffix');
			$arrt=array_values($tuan);
			if (is_array($arrt[0])) {//多个数据
				foreach ($tuan as &$value) {
					$value['_image_large']=$value['image']?$prefix.$value['image'].$LSuffix:'';
					$value['_image_small']=$value['image']?$prefix.getSmallImage($value['image']).$SSuffix:'';
					$value['_image1_large']=$value['image1']?$prefix.$value['image1'].$LSuffix:'';
					$value['_image1_small']=$value['image1']?$prefix.getSmallImage($value['image1']).$SSuffix:'';
					$value['_image2_large']=$value['image2']?$prefix.$value['image2'].$LSuffix:'';
					$value['_image2_small']=$value['image2']?$prefix.getSmallImage($value['image2']).$SSuffix:'';
				}
			}else{//单个数据
				$value['_image_large']=$value['image']?$prefix.$value['image'].$LSuffix:'';
				$value['_image_small']=$value['image']?$prefix.getSmallImage($value['image']).$SSuffix:'';
				$value['_image1_large']=$value['image1']?$prefix.$value['image1'].$LSuffix:'';
				$value['_image1_small']=$value['image1']?$prefix.getSmallImage($value['image1']).$SSuffix:'';
				$value['_image2_large']=$value['image2']?$prefix.$value['image2'].$LSuffix:'';
				$value['_image2_small']=$value['image2']?$prefix.getSmallImage($value['image2']).$SSuffix:'';
			}
		}
	}


	static function fillTuanInfo(&$resArr){
		if (count($resArr)==0)return;
		$teamIds=Utils::arrVauleList($resArr,'team_id');
		$tuans=self::teamsByIds($teamIds,false,true);
		PartnerEngine::fillPartnerInfo($tuans);
		self::fillImagePath($tuans);
		self::fillExpressInfo($tuans);
		foreach ($resArr as &$value) {
			if ($tuans[$value['team_id']]) {
				$value['team']=$tuans[$value['team_id']];
				unset($value['team']['detail']);
				unset($value['team']['systemreview']);
			}
		}
	
	}


	static function fillExpressInfo(&$teams){
		if (is_array($teams)&&$teams) {
			$arrt=array_values($teams);
			if (is_array($arrt[0])) {//多个数据
				foreach ($teams as &$value) {
					self::fillExpressInfo($value);
				}
			}else{//单个数据
				$express=unserialize($teams['express_relate']);
				if ($express&&$teams['delivery']=='express') {
					$eids=Utils::arrVauleList($express,'id');
					$expArr=self::expressByIds($eids,true);
					foreach ($express as &$v) {
						$v['name']=$expArr[$v['id']];
						$v['name']=$v['name']['name'];
					}
					$teams['express_relate']=$express;
				}else{
					$teams['express_relate']=NULL;
				}
			}
		}
	}


//读取快递信息
	static function express($id='')
	{
		$db=M('category');
		$map['zone']=array('EQ','express');
		$map['display']=array('EQ','Y');
		$data=$db->where($map)->field('id,name')->select();
		$res=array();
		foreach ($data as $key => $value) {
			$res[$value['id']]=$value;
		}
		if ($id) {
			return $res[$id];
		}
		return $res;
	}

	static function expressByIds($ids,$maping=false){
		$where['zone']=array('EQ','express');
		$where['display']=array('EQ','Y');
		return self::objsByIds('category',$ids,$where,$maping);
	}


	static function expressById($id){
		$ex=self::expressByIds($id);
		return $ex[0];
	}


//余额支付
	function creditPay($orderId){
		if (!$orderId)return false;
		$u=new UserEngine();
		$order=$u->userOrderById($orderId,false);
		$user=$u->userInfo();
		$team=TuanEngine::teamById($order['team_id']);
		if ($team&&$order&&$user&&($order['user_id']==$user[id])) {
			if ($order['state']=='pay') {
				// var_dump($order);
				$this->error=C('ERR_402');
				$this->error['text']='订单已付款，请勿重复支付';
				return false;
			}
			if($user['money']<$order['origin']){
				$this->error=C('ERR_402');
				$this->error['text']='余额不足';
				return false;
			}
			if ($team['end_time']<time()) {
				$this->error=C('ERR_402');
				$this->error['text']='本商品已经过期';
				return false;
			}
			if ($team['max_number']>0&&$team['now_number']+$order['quantity']>$team['max_number']) {
				$this->error=C('ERR_400');
				$this->error['text']="本商品不足,无法购买";
				return false;
			}
			return Utils::teamCreditPay($order);
			// return true;
		}else{
			$this->error=C('ERR_500');
			return false;
		}
	}

	function buynew($sid,$uid){
		$ex_con = array(
				'user_id' => $uid,
				'team_id' => $sid
		);
		$order = OrderEngine::objFromTable('order', $ex_con);
		
		$res=$this->goodById($sid);
		
		if (!$res) {
			$this->error=C('ERR_400');
			$this->error['text']='本商品不存在';
			return false;
		}
		if (!$order) {
			$user=new UserEngine();
			if ($res['id']>0) {
				$userInfo=$user->userInfo($uid);
		
				$db=M('order');
				$data['user_id']=$uid;
				$data['allowrefund']=$res['allowrefund'];//是否可以退款
				$data['quantity']=1;
				$data['team_id']=$res['id'];
				$data['city_id']=$res['city_id'];
				$data['partner_id']=$res['partner_id']?$res['partner_id']:0;
		
				$data['price']=$res['team_price'];
				$data['origin']=$data['price']*1;//商品价格
		
				$data['credit']=$userInfo['money'];
				$data['service']='tenpay';
				$data['state']='unpay';
		
				$data['mobile']=$userInfo['mobile'];
				$data['condbuy']='';
		
				$data['express']='N';
				$data['express_id']='0';
				$data['realname']='';
				$data['address']='';
				$data['zipcode']='';
					
				$data['remark']='';
			
				$randid = strtolower(Utils::ztGenSecret(4));
				//生成新订单
				// echo "生成新订单";
				$data['create_time']=time();//创建时间
				$index=$db->add($data);
				// echo $db->getLastSql();
				$pay_id = "go-".$index."-".$num."-".$randid;
				$newdata=array();
				$newdata['pay_id']=$pay_id;
				$db->where('id=%d',$index)->save($newdata);
				Utils::recordOrder($uid,$index,$from);
				$ress = $db->where('id=%d',$index)->find();
				if ($ress) {
					$couponcode = $this->createCoupon($ress, $res, $userInfo);
					$ress['money']=$userInfo['money']-$ress['origin'];
					$ress['money']=$ress['money']<0?0:$ress['money'];
					$host=Utils::host();
						
					$returnData =  Utils::simplifyArr($ress,array('buy_id','admin_id','city_id','card_id','trade_no','rereason','retime','card','pay_time','comment_content','comment_display','comment_grade','comment_wantmore','comment_time','sms_express','luky_id','adminremark'));
					$returnData['couponcode'] = $couponcode;
					return $returnData;
					
				}else{
					$this->error=C('ERR_500');
					return false;
				}
			}else{
				$this->error=C('ERR_500');
				return false;
			}
		}
		else {
			$user=new UserEngine();
			$userInfo=$user->userInfo($uid);
			$couponcode = $this->createCoupon($order, $res, $userInfo);
			$returnData['couponcode'] = $couponcode;
			return $returnData;
		}
	}
	
	function createCoupon($order, $team, $userInfo){
		$db=M('coupon');
		$id = (ceil(time()/100)+rand(10000000,40000000)).rand(1000,9999);
		$id = Utils::VerifyCode($id);
		$map['user_id'] = array('EQ', $order['user_id']);
		$map['team_id'] = array('EQ', $team['id']);
		$cv = $db->where($map)->find();
		if (!$cv) {
			$coupon = array(
						'id' => $id,
						'user_id' => $order['user_id'],
						'buy_id' => $order['buy_id'],
						'partner_id' => $team['partner_id'],
						'order_id' => $order['id'],
						'credit' => $team['credit'],
						'team_id' => $order['team_id'],
						'secret' => '',
						'expire_time' => $team['expire_time'],
						'create_time' => time(),
				);
				$index=$db->add($coupon);
		}
		$couponcode = '';
		if (isset($userInfo['couponcode']) && !empty($userInfo['couponcode'])) {
			$couponcode = $userInfo['couponcode'];
		}
		else {
			$couponcode = $this->createCouponcode($order['user_id']);
		}
		return $couponcode;
	}
	
	public function createCouponcode($user_id){
		$couponcode = (rand(1000,9999)).rand(100,999);
		$db = M('user');
		$map['id']=array('EQ',$user_id);
		$map['couponcode']=array('EQ',$couponcode);
		$cv = $db->where($map)->find();
		if($cv){
			$this->createCouponcode($user_id);
		}
		else {
			$data = array();
			$data['couponcode'] = $couponcode;
			$result = $db->where('id=%d',$user_id)->save($data);
			if ($result) {
				return $couponcode;
			}
			return '';
		}
	}
}