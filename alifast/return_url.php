<?php 
require_once("alipay.config.php");
require_once("lib/alipay_notify.class.php");
$alipayNotify = new AlipayNotify($aliapy_config);
$verify_result = $alipayNotify->verifyReturn();
if($verify_result) {
	$ali_user = ZUser::Check_alifast($_GET['user_id'],$_GET['real_name'],$_GET['email']);
	if($ali_user){
	   Session::Set('user_id', $ali_user['id']);
	   Session::Set('ali_token', $_GET['token']);
	   ZCredit::Login($ali_user['id']);
       //etao专用
           if($_GET['target_url'] != "") {
              $url = $_GET['target_url'];
              echo "<script>window.location='$url';</script>";
              exit();
             }
	   redirect(get_loginpage(WEB_ROOT . '/index.php'));
	}else{ 
       Session::Set('error', '验证失败');
	   redirect(WEB_ROOT . '/index.php');
	}

     
}
else {
    echo "验证失败";
}
?>
