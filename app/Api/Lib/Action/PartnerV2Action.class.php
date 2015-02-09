<?php
//商户
class PartnerV2Action extends CommonAction{
	

	function index(){
    	if (!APP_DEBUG)return;
		header('Content-Type:text/html; charset=utf-8');
		echo '<html>';
		echo '<body>';
		echo '<pre/>';
		echo 'Partner(商家)模块接口V2:<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Partner/login&help">/login&help</a> ：登录（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Partner/login&name=zhengongfu&pwd=123">/login&name=zhengongfu&pwd=123</a> ：根据商户名<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Partner/logout&help">/logout&help</a> ：登出（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Partner/logout">/logout</a> ：登出<br/>';
		echo '|<br/>';
		echo '|-<a href="'.__APP__.'?s=Partner/verifyCoupon&help">/verifyCoupon&help</a> ：获验证优惠卷有消息（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Partner/verifyCoupon&code=1">/verifyCoupon&code=1</a> ：获验证优惠卷有消息<br/>';	
		echo '|  |-<a href="'.__URL__.'/../../../ajax/coupon.php?action=consume&id=1&secret=123">/ajax/coupon.php&action=consume&id=1&secret=123</a> ：优惠卷消费接口<br/>';
		echo '|<br/>';
		echo '</body>';
		echo '</html>';
    }
    //http://localhost/



    function login(){
		$this->hinfo='
		/**
		*商户登录接口
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
		$partner=new PartnerEngine();
		if ($name!=''&&$pwd!='') {
			$res=$partner->login($name,$pwd);
			if ($res) {
				$data['status'] = 1;
				$data['result']	= $res?$res:array();
			}else{
				$data['status'] = 0;
				$data['error']	= $partner->getError();
			}
		}else{
			$data['status'] = 0;
			$data['error']	= C('ERR_400');
		}
		if ($data['status']!=1)$partner->logout();
		$this->out($data); 

    }

    function logout(){
		$this->hinfo='
		/**商户登出接口
		*啥也不用传···直接掉就登出(不检查是否已经登录，属于一个不用理会返回结果的接口)
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();
		$p=new PartnerEngine();
		$p->logout();
		$data['status'] = 1;
		$data['result']	= 'success';
		$this->out($data); 
    }

	function checkCoupon(){
		$this->hinfo='
		/**
		*商户验证优惠卷
		*	
		*@param:
		*	|code 	 	:优惠卷code
		*
		*状态码(status)：1:正常,0:失败,-1:需要登录
		*
		*是否需要登录:YES
		*请求方式:GET
		*@return maxied 操作结果
		*/';

		$this->_help();
		$code=$this->_get('code');
		if ($code&&PartnerEngine::isLogin()) {
			$engine=new PartnerEngine();
			$pid=PartnerEngine::getPartnerId();
			$res=$engine->verifyCoupon($code,$pid);
			$data['status'] = $res['code'];
			unset($res['code']);
			$data['result']	= $res['info'];
			if($res['team'])$data['team']= $res['team'];
		}else{
			$data['status'] = -1;
			$data['error']	= C('ERR_400');
		}
		$this->out($data); 
	}

	function execCoupon(){
		$this->hinfo='
		/**
		*商户验证优惠卷
		*	
		*@param:
		*	|code 	 	:优惠卷code
		*
		*状态码(status)：1:正常,0:失败,-1:需要登录
		*
		*是否需要登录:YES
		*请求方式:GET
		*@return maxied 操作结果
		*/';

		$this->_help();
		$code=I('code');
		$pwd=I('sec');

		$host=Utils::host();
		$type='execCoupon';
		$token=urlencode(Utils::cfile($type));
		$api = $host.'/app/Api/func.php?token='.$token.'&type='.$type."&code=$code&sec=$pwd";
		pr($api);
		$res = zthttp_get($api);
		$res=json_decode($res,true);
		var_dump($res);
	}


}

?>