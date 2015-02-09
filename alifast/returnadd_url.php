<?php
require_once("alipayadd.config.php");
require_once("lib/alipay_notify.class.php");
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyNotify();
if($verify_result) {//验证成功
      

	$receive_address = (get_magic_quotes_gpc()) ? stripslashes($_POST['receive_address']) : $_POST['receive_address'];
	$ali_address = array();
	$receive_address = xml_unserialize($receive_address);
	if($receive_address){
	   $ali_address['prov'] = $receive_address['receiveAddress']['prov'];
	   $ali_address['city'] = $receive_address['receiveAddress']['city'];
	   $ali_address['area'] = $receive_address['receiveAddress']['area'];
	   $ali_address['address'] = $receive_address['receiveAddress']['address'];
	   $ali_address['fullname'] = $receive_address['receiveAddress']['fullname'];
	   $ali_address['mobile_phone'] = $receive_address['receiveAddress']['mobile_phone'];
	   $ali_address['post'] = $receive_address['receiveAddress']['post'];
	
	}
	if($ali_address){
       Session::Set('ali_add', $ali_address);
       redirect(get_loginpage(WEB_ROOT . '/index.php'));
	}else{ 
       Session::Set('error', '获取物流地址失败');
	   redirect(get_loginpage(WEB_ROOT . '/index.php'));
	}
	
}
else {
    //验证失败
    //如要调试，请看alipay_notify.php页面的verifyNotify函数，比对sign和mysign的值是否相等，或者检查$veryfy_result有没有返回true
    echo "验证失败";
}
?>