<?php

class PayReturnAction extends CommonAction {

//支付宝支付成功通知地址
    function notifyAlipay(){

    	$notify_data=$_POST["notify_data"];
		$sign = $_POST["sign"];
	    //验证签名
	    $isVerify = AlipayFuns::verify($notify_data, $sign);
	    //如果验签没有通过
	    if(!$isVerify){
	    	$v_oid = Utils::getDataForXML($notify_data, '/notify/out_trade_no');
	    	$lines='('.__CLASS__.':'.(__LINE__+1).')';
	    	Utils::ilog('支付宝-签名验证失败,订单ID:'.$v_oid.' '.$lines,'PAYERR');
	    	die("fail");
	    }
	    //获取交易状态
	    $trade_status = Utils::getDataForXML($notify_data, '/notify/trade_status');
	    //判断交易是否完成
	    if($trade_status == "TRADE_FINISHED"){

	    	$v_oid = Utils::getDataForXML($notify_data, '/notify/out_trade_no');
			$trade_no = Utils::getDataForXML($notify_data, '/notify/trade_no');
			$total_fee=Utils::getDataForXML($notify_data, '/notify/total_fee');
			$v_amount = Utils::moneyit($total_fee);
			list($_, $order_id, $_, $_) = explode('-', $v_oid, 4);

			$currency = 'CNY';
			$service = 'alipay';
			$bank = '支付宝';
			$res=Utils::teamOtherPay($order_id, $v_oid, $v_amount, $currency, $service, $bank,$trade_no);
			
	    	if($res===true){
				die('success');
			}else{
				$lines='('.__CLASS__.':'.(__LINE__+1).')';
				Utils::ilog('支付宝-修改订单状态失败,订单ID:'.$v_oid.' '.$lines,'PAYERR');
				die('fail');
			}
	    }else if($trade_status == "WAIT_BUYER_PAY"){
	    	die('success');
	    }else{
	    	die("fail");
	    }

	}


	//财付通支付成功通知地址
    function notifyTenpay(){
    	$pars=$_GET;
    	unset($pars['_URL_']);
    	$tenpay = Utils::getTenpay();
    	$v_oid = $this->_get('sp_billno');
    	if (TenpayFuns::isTenpaySign($pars,$tenpay['sec'])) {//通过验证
			$trade_no = $this->_get('transaction_id');
			$v_amount = Utils::moneyit($this->_get('total_fee')*0.01);
			list($_, $order_id, $_, $_) = explode('-', $v_oid, 4);
			
			$currency = 'CNY';
			$service = 'tenpay';
			$bank = '财付通';
			$res=Utils::teamOtherPay($order_id, $v_oid, $v_amount, $currency, $service, $bank,$trade_no);
			if($res===true){
				die('success');
			}else{
				$lines='('.__CLASS__.':'.(__LINE__+1).')';
				Utils::ilog($bank.'-修改订单状态失败,订单ID:'.$v_oid.' '.$lines,'PAYERR');
				die('fail');
			}	
    	}else {
    		$lines='('.__CLASS__.':'.(__LINE__+1).')';
	    	Utils::ilog($bank.'-签名验证失败,订单ID:'.$v_oid.' '.$lines,'PAYERR');
    		die('fail');
    	}
	}


//银联支付回调
	function notifyUmspay(){
		$ums=Utils::buildUmspay();
		if (!$ums)return;
		$result=$ums->notify();
		if ($result&&$_POST['TransState']==1) {//通过验证
			$v_oid=$this->_post('MerOrderId');

			list($_, $order_id, $_, $_) = explode('-', $v_oid, 4);

			$trade_no=$this->_post('TransId');
			$money=$this->_post('TransAmt');
			
			$money=Utils::moneyit($money*0.01);
			
			$currency = 'CNY';
			$service = 'umspay';
			$bank = '银联全民捷付';
			$res=Utils::teamOtherPay($order_id, $v_oid, $money, $currency, $service, $bank,$trade_no);
			$ress['order_id']=$order_id;
			$ress['v_oid']=$v_oid;
			$ress['money']=$money;
			$ress['currency']=$currency;
			$ress['service']=$service;
			$ress['trade_no']=$trade_no;
	    	if($res===true){
				die('success');
			}else{
				$lines='('.__CLASS__.':'.(__LINE__+1).')';
				Utils::ilog('银联-修改订单状态失败,订单ID:'.$v_oid.' '.$lines,'PAYERR');
				die('fail');
			}
		}
		// Utils::log2db('商户通知响应报文:'.json_encode($result));
		exit(json_encode($result));
	}

//微信支付回调
	function notifyWeChatpay(){
		$pars=$_GET;
		unset($pars['_URL_']);
		$wePaykey=C('wechatPayPartnerKey');
		$v_oid = $this->_get('out_trade_no');//商户系统的订单号,与请求一致
		$res = WeChatPayFuns::verifySign($pars,$wePaykey);
		// Utils::log2db('微信回调',json_encode($pars));
		if ($res===true) {
			// Utils::log2db('微信回调','成功通过验证');
			$trade_no = $this->_get('transaction_id');
			$v_amount = Utils::moneyit($this->_get('total_fee')*0.01);
			list($_, $order_id, $_, $_) = explode('-', $v_oid, 4);
			
			$currency = 'CNY';
			$service = 'wechatpay';
			$bank = '微信支付';
			$res=Utils::teamOtherPay($order_id, $v_oid, $v_amount, $currency, $service, $bank,$trade_no);
			if ($res===true) {
				// Utils::log2db('微信回调','支付成功');
				die('success');
			}else{
				// Utils::log2db('微信回调','支付失败');
				$lines='('.__CLASS__.':'.(__LINE__+1).')';
				Utils::ilog($bank.'-修改订单状态失败,订单ID:'.$v_oid.' '.$lines,'PAYERR');
				die('fail');
			}
		}else{
			// Utils::log2db('微信回调','订单验证失败');
    		$lines='('.__CLASS__.':'.(__LINE__+1).')';
	    	Utils::ilog($bank.'-签名验证失败,订单ID:'.$v_oid.' '.$lines,'PAYERR');
    		die('fail');
		}	
	}

	function payResultPage(){
		$id=$this->_get('id');
		$order=UserEngine::userOrderById($id);
		if ($order['state']=='pay') {
			$this->display('paySuccess');	
		}else{
			$this->assign('payid', $order['pay_id']);
			$this->display('payFail');	
		}
	}

}