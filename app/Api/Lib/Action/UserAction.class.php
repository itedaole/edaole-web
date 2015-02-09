<?php


class UserAction extends CommonAction {
    public function index(){
    	if (!APP_DEBUG)return;
		header('Content-Type:text/html; charset=utf-8');
		echo '<html>';
		echo '<body>';
		echo '<pre/>';
		echo 'User模块接口:<br/>';
		echo '|-<a href="'.__APP__.'?s=User/register&help">/register?help</a> 注册（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/register&name=aaa&pwd=123&email=123@123.com">/register?name=aaa&pwd=123&email=123@123.com</a> ：注册<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=User/mobileVerify&help">/mobileVerify?help</a> ：请求手机验证码（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/mobileVerify&mobile=123456">/mobileVerify?mobile=123456</a> ：请求手机验证码<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/mobileVerify&vcode=12345">/mobileVerify?vcode=12345</a> ：验证验证码合法性<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=User/login&help">/login?help</a> ：登录（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/login&name=aaa&pwd=123">/login?name=aaa&pwd=123</a> ：根据用户名或者邮箱登录<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/login">/login</a> ：获取当前登录用户的信息<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=User/isLogin&help">/isLogin?help</a> ：判断登录（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/isLogin">/isLogin</a> ：判断登录<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=User/logout&help">/logout?help</a> ：登出（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/logout">/logout</a> ：登出<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=User/getCoupon&help">/getCoupon?help</a> ：：获取当前登录用户的团购卷（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/getCoupon&type=0">/getCoupon?type=0</a> ：获取当前登录用户的团购卷<br/>';
		echo '|<br/>';;
		echo '|-<a href="'.__APP__.'?s=User/getCouponNew&help">/getCouponNew?help</a> ：：获取当前登录用户的优惠码（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/getCouponNew&user_id=1">/getCouponNew&user_id=1</a> ：获取当前登录用户的优惠码<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=User/getOrders&help">/getOrders?help</a> ：：获取当前登录用户的订单（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/getOrders">/getOrders</a> ：获取当前登录用户的订单<br/>';	
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=User/getCouponcode&help">/getCouponcode?help</a> ：：根据用户id获取用户优惠码（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=User/getCouponcode&id=1">/getCouponcode&id=1</a> ：获取当前登录用户的订单<br/>';
		echo '|<br/>';
		echo '</body>';
		echo '</html>';
    }
/**
user
*/

//获取用户优惠卷
	function getCoupon(){
		$this->hinfo='
		/**
		*获取登录用户的优惠卷
		*参数
		*@param string type:用户名,默认为0(未使用),1(已使用),2(已过期),3(全部)
		*@param string express:合并快递（新加字段，会把快递信息也放里面）
		*
		*
		*状态码(status)：1:正常,0:失败,-1:需要登录
		*
		*是否需要登录:YES
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();

		$page=defIntVaule(I('page'),1,false,1);
		$count=defIntVaule(I('count'),1,200,200);

		$type=intval($this->_get('type'));
		$user=new UserEngine();
		if ($user->isLogin()) {
			$user->queryUMSOrder();
			$res=$user->userCoupon($type,true);
			if (isset($_GET['express'])) {
				$order=$user->userOrders(NULL,NULL,true,$count,$page);
				$tuan=new TuanEngine();
				$exinfon=$tuan->express();
				$ex=array();
				foreach ($order as $value) {
					if ($value['express']=='Y'&&$value['state']=='pay'){
						$value['express_name']=$exinfon[$value['express_id']];
						$ex[]=$value;
					}
				}
				$res=array('coupon'=>$res,'express'=>$ex);
			}
			if ($res!==false) {
				$data['status'] = 1;
				$data['result']	= $res?$res:array();
			}else{
				$data['status'] = 0;
				$data['error']	= $user->getError();
			}
		}else{
			$data['status'] = -1;
			$data['error']	= C('ERR_401');
		}
		$this->out($data); 
	}

	function isLogin(){
		$user=new UserEngine();
		if ($user->isLogin()) {
			$data['status'] = 1;
			$data['uid']	= $user->userId();
		}else{
			$data['status'] = 0;
			$data['uid']	= 0;
		}
		$this->out($data); 
	}

	function getOrders(){
		$this->hinfo='
		/**
		*获取登录用户的订单
		*
		*1.7.0新参数
		*@param int page 第几页,从1开始，不传默认为1
		*@param int count 每页多少个，不传默认10（团长可配）
		*
		*状态码(status)：1:正常,0:失败,-1:需要登录
		*
		*是否需要登录:YES
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help($info);

		$page=defIntVaule(I('page'),1,false,1);
		$count=defIntVaule(I('count'),1,200,10);
		$pagging=false;
		if (isset($_GET['page'])||isset($_GET['count'])) {
			$pagging=true;
		}
		$user=new UserEngine();
		if ($user->isLogin()) {
			$page_current=$page;
			$res=$user->userOrders(NULL,NULL,true,$count,$page);
			if ($res!==false) {
				$data['status'] = 1;
				if ($pagging) {
					$data['result'] = array('exitf'=>array('count'=>$count,'page_current'=>$page_current,'page_count'=>$page),'data'=>$res);
				}else{
					$data['result']	= $res;
				}
			}else{
				$data['status'] = 0;
				$data['error']	= $user->getError();
			}
		}else{
			$data['status'] = -1;
			$data['error']	= C('ERR_401');
		}
		$this->out($data); 
	}

	function register(){
		$this->hinfo='
		/**
		*注册接口
		*@param string name:用户名 必填
		*@param string email:邮箱 必填
		*@param string pwd:密码 必填
		*@param string mobile:电话 (已废弃，改为验证短信的那个号码)
		*@param string vcode:验证码
		*
		*状态码(status)：1:正常,0:失败
		*
		*通信协议:HTTP
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();
		$name=trim($this->_get('name'));
		$email=trim($this->_get('email'));
		$pwd=$this->_get('pwd');
		// $tell=trim($this->_get(mobile));
		$vcode=intval($this->_get('vcode'));

		$user=new UserEngine();
		if ($name!=''&&$pwd!=''&&$email!=''&&strstr($email,'@')) {
			$res=$user->register($name,$vcode,$email,$pwd);
			if ($res) {
				$data['status'] = 1;
				$data['result']	= $res?$res:array();
			}else{
				$data['status'] = 0;
				$data['error']	= $user->getError();
			}
		}else{
			$data['status'] = 0;
			$data['error']	= C('ERR_400');
		}
		if ($data['status']!=1)$user->logout();
		$this->out($data); 
	}


    public function login(){
		$this->hinfo='
		/**
		*登录接口
		*@param string name:用户名/邮箱(判断方式为，带@的就为邮箱)
		*@param string pwd:密码
		*
		*!服务器不对密码做去空格处理,如果有需要亲自己处理
		*
		*状态码(status)：1:正常,0:失败
		*
		*通信协议:HTTP
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();
		$name=trim($this->_get('name'));
		$pwd=$this->_get('pwd');
		$user=new UserEngine();
		if ($name!=''&&$pwd!='') {
			$res=$user->login($name,$pwd);
			if ($res) {
				$data['status'] = 1;
				$data['result']	= $res?$res:array();
			}else{
				$data['status'] = 0;
				$data['error']	= $user->getError();
			}
		}else if($user->isLogin()){
				$data['status'] = 1;
				$data['result']	= $user->userInfo();
		}else{
			$data['status'] = 0;
			$data['error']	= C('ERR_400');
		}
		if ($data['status']!=1)$user->logout();
		$this->out($data); 
    }

    public function logout(){
		$this->hinfo='
		/**
		*啥也不用传···直接掉就登出(不检查是否已经登录，属于一个不用理会返回结果的接口)
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();
		$user=new UserEngine();
		$user->logout();
		$data['status'] = 1;
		$data['result']	= 'success';
		$this->out($data); 
    }

    function mobileVerify(){
    	$this->hinfo='
		/**
		*获取短信验证码接口、验证code合法性接口
		*
		*如果同时传入的是验证码则返回这个验证码的合法性，如果只传手机号，就会给手机号发验证码
		*
		*@param string mobile:手机号码
		**@param string vcode:验证码 (验证码为5位整形数字)
		*
		* 请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();
		$number=trim($this->_get('mobile'));
		$vcode=intval($this->_get('vcode'));
		
		if ($vcode) {
			$data['status'] = 1;
			$data['result']	= 'success';
			//注释短信验证
			/*if(Utils::checkMobileVerifyCode($vcode)){
				if (UserEngine::isLogin()){
					$user=new UserEngine();
					$res=$user->updateMobile($user->userId(),Utils::getVerifyMobile());
					if ($res&&!APP_DEBUG) {
						Utils::destroyMobileVerifyCode();
					}
				}

				$data['status'] = 1;
				$data['result']	= 'success';
			}else{
				$data['status'] = 0;
				$data['result']	= 'fail';
			}*/
		}else if ($number) {
			$user=new UserEngine();
			//注释短信发送
			$result= true ;//$user->mobileVerify($number);
			if($result===true){
				$data['status'] = 1;
				$data['result']	= 'success';
			}else{
				$data['status'] = 0;
				$data['error']	= $result;
			}
		}else{
				$data['status'] = 0;
				$data['error']	= C('ERR_400');
		}
		$this->out($data); 
	}
	
	function getCouponcode(){
		$this->hinfo='
		/**
		*根据用户id获取用户的优惠码
		*
		*新参数
		*@param int id 用户id
		*
		*状态码(status)：1:正常,0:失败,-1:需要登录
		*
		*是否需要登录:YES
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help($info);
		$id=trim($this->_get('id'));
		$user=new UserEngine();
		$userinfo = $user->userInfo($id);
		if ($userinfo) {
			$data['status'] = 1;
			$data['result'] = array('id'=>$id,'couponcode'=>$userinfo['couponcode']);
		}
		else {
			$data['status'] = 0;
			$data['result'] = C('ERR_400');
		}
		$this->out($data);
	}
	
	//获取用户优惠卷
	function getCouponNew(){
		$this->hinfo='
		/**
		*获取登录用户的优惠码列表
		*参数
		*@param string user_id:用户id
		*
		*
		*状态码(status)：1:正常,0:失败,-1:需要登录
		*
		*是否需要登录:YES
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();
	
		$page=defIntVaule(I('page'),1,false,1);
		$user_id=trim($this->_get('user_id'));
		$user=new UserEngine();
		if ($user->isLogin()) {
			$map['user_id']=$user_id;
			$res=TuanEngine::objsFromTable('coupon',$map,false,'create_time DESC');
			$teamIds = Utils::GetColumn($res,'team_id');
			$teams = TuanEngine::teamsByIds($teamIds);
			$teams = array(current($teams));
			if ($res!==false) {
				$data['status'] = 1;
				$data['result']	= $teams?$teams:array();
			}else{
				$data['status'] = 0;
				$data['error']	= $user->getError();
			}
		}else{
			$data['status'] = -1;
			$data['error']	= C('ERR_401');
		}
		$this->out($data);
	}
}