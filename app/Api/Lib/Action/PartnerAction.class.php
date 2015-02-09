<?php
//商户
class PartnerAction extends CommonAction{
	

	function index(){
    	if (!APP_DEBUG)return;
		header('Content-Type:text/html; charset=utf-8');
		echo '<html>';
		echo '<body>';
		echo '<pre/>';
		echo 'Partner(商家)模块接口:<br/>';
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
		echo '|-<a href="'.__APP__.'?s=Partner/ls&help">/ls&help</a> ：获取所有带经纬度商户（帮助）<br/>';
		echo '|  |-<a href="'.__APP__.'?s=Partner/ls&lng=100&lat=38.2">/ls&lng=100&lat=38.2</a> ：获取所有带经纬度商户<br/>';	
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

    function ls(){
    	$this->hinfo='
		/**获取所有商户
		*
		*@param lng:经度（传入经纬度会计算距离，不传就不算）
		*@param lat:纬度
		*
		*@param range:范围(默认为配置文件D_PARTNER_LS_R的值)，单位米
		*
		*请求方式:GET
		*@return maxied 操作结果，
		*
		*/';
		$this->_help();

		$lng=$this->_get('lng');
		$lat=$this->_get('lat');

		$range=intval($this->_get('range'));
		$Drange=intval(C('D_PARTNER_LS_R'));
		if ($range<=0||$range>$Drange) {
			$range=$Drange;
		}

		$p=new PartnerEngine();
		$data['status'] = 1;
		$data['result']	= $p->ls($lng,$lat,$range,$count);
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

	function verifyCoupon(){
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

	function detail_mobile() {
		$id=intval($this->_get('id'));
		$res=PartnerEngine::partnerById($id);
		foreach ($res as $key => $value) {
			$this->assign($key, $value);
		}
		$this->display();
	}
}

?>