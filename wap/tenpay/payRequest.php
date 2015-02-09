<?php
//---------------------------------------------------------
//财付通即时到帐支付请求示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require_once ("classes/RequestHandler.class.php");
require ("classes/client/ClientResponseHandler.class.php");
require ("classes/client/TenpayHttpClient.class.php");
require ("config.php");

require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');


need_login(true);

if (is_post()) {
	$order_id = abs(intval($_POST['order_id']));
} else {
	$order_id = $id = abs(intval($_GET['id']));
}
if(!$order_id || !($order = Table::Fetch('order', $order_id))) {
	redirect( 'index.php');
}

/* generator unique pay_id */
if (! ($order['pay_id'] 
			&& (preg_match('#-(\d+)-(\d+)-#', $order['pay_id'], $m) 
				&& ( $m[1] == $order['id'] && $m[2] == $order['quantity']) )
	  )) {
	$randid = strtolower(Utility::GenSecret(4, Utility::CHAR_WORD));
	$pay_id = "go-{$order['id']}-{$order['quantity']}-{$randid}";
	Table::UpdateCache('order', $order['id'], array(
				'pay_id' => $pay_id,
				));
	$order['pay_id'] = $pay_id;
}

$orderid=$order["pay_id"];
$money=$order["price"];
//商户返回地址
$url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') ? 'https://' : 'http://';
$url .= str_ireplace('localhost', '127.0.0.1', $_SERVER['HTTP_HOST']) . $_SERVER['SCRIPT_NAME'];
$callback_url = str_ireplace('payRequest', 'payReturnUrl', $url);
$notify_url = str_ireplace('payRequest', 'payNotifyUrl', $url);

//4位随机数
$randNum = rand(1000, 9999);

//订单号，此处用时间加随机数生成，商户根据自己情况调整，只要保持全局唯一就行
list($_, $order_id, $city_id, $_) = explode('-', $orderid, 4);
$out_trade_no =trim($orderid.$randNum);// date("YmdHis") . $randNum;
// $out_trade_no =date("YmdHis") . $randNum;

/* 创建支付请求对象 */
$reqHandler = new RequestHandler();
$reqHandler->init();
$reqHandler->setKey($key);
//$reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");
//设置初始化请求接口，以获得token_id
$reqHandler->setGateUrl("http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_init.cgi");


$httpClient = new TenpayHttpClient();
//应答对象
$resHandler = new ClientResponseHandler();
//----------------------------------------
//设置支付参数 
//----------------------------------------

$reqHandler->setParameter("total_fee",intval($money*100));  //总金额
//用户ip
$reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
$reqHandler->setParameter("ver", "2.0");//版本类型
$reqHandler->setParameter("bank_type", "0"); //银行类型，财付通填写0
$reqHandler->setParameter("callback_url", $callback_url);//交易完成后跳转的URL
$reqHandler->setParameter("bargainor_id", $partner); //商户号
$reqHandler->setParameter("sp_billno", $out_trade_no); //商户订单号
$reqHandler->setParameter("notify_url", $notify_url);//接收财付通通知的URL，需绝对路径
$reqHandler->setParameter("desc", "在线支付");
$reqHandler->setParameter("attach", "");


$httpClient->setReqContent($reqHandler->getRequestURL());

//后台调用
if($httpClient->call()) {
	//echo $httpClient->getResContent();
	$resHandler->setContent($httpClient->getResContent());
	//获得的token_id，用于支付请求
	$token_id = $resHandler->getParameter('token_id');
	$reqHandler->setParameter("token_id", $token_id);
	
	//请求的URL
	//$reqHandler->setGateUrl("https://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi");
	//此次请求只需带上参数token_id就可以了，$reqUrl和$reqUrl2效果是一样的
	//$reqUrl = $reqHandler->getRequestURL(); 
	$reqUrl = "http://wap.tenpay.com/cgi-bin/wappayv2.0/wappay_gate.cgi?token_id=".$token_id;
		
}
echo "<script>location.href='".$reqUrl."'</"."script>";
exit();
?>
 <!DOCTYPE wml PUBLIC "-//WAPFORUM//DTD WML 1.1//EN"
 "http://www.wapforum.org/DTD/wml_1.1.xml">
   <wml>
     <head>
       <meta http-equiv="Cache-Control" content="max-age=0" forua="true"/>
       <meta http-equiv="Cache-control" content="must-revalidate" />
       <meta http-equiv="Cache-control" content="private" />
       <meta http-equiv="Cache-control" content="no-cache" />
     </head>
     <card id="wappay" title="财付通wap手机支付示例">
     <p>
     	<a href="<?php echo $reqUrl; ?>">财付通WAP支付</a>
     </p>
   </wml>