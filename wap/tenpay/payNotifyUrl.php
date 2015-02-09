<?php

//---------------------------------------------------------
//财付通即时到帐支付后台回调示例，商户按照此文档进行开发即可
//---------------------------------------------------------

require ("classes/ResponseHandler.class.php");
require ("classes/WapNotifyResponseHandler.class.php");
require ("config.php");
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');


/* 创建支付应答对象 */
$resHandler = new WapNotifyResponseHandler();
$resHandler->setKey($key);

//判断签名
if($resHandler->isTenpaySign()) {
	
	//商户订单号
	$bargainor_id = $resHandler->getParameter("bargainor_id");
	
	//财付通交易单号
	$transaction_id = $resHandler->getParameter("transaction_id");
	//金额,以分为单位
	$total_fee = $resHandler->getParameter("total_fee");
	
	//支付结果
	$pay_result = $resHandler->getParameter("pay_result");
	$sp_billno = $resHandler->getParameter("sp_billno");
	if( "0" == $pay_result  ) {
		
			list($_, $order_id, $city_id, $_) = explode('-', $sp_billno, 4);
			
			$total_fee=$total_fee/100;
			$r3_Amt=number_format($total_fee,2,".","");
			
			$currency = 'CNY';
			$service = 'tenpay';
			$bank ='财付通手机支付';
			ZOrder::OnlineIt($order_id, $sp_billno, $r3_Amt, $currency, $service, $bank);
			// redirect(WEB_ROOT . "/waptuan/order.php?id={$order_id}");
		
		echo 'success';
	}
	else
	{
		echo 'fail';
	} 
	
} else {
	//回调签名错误
	echo "fail";
}


?>