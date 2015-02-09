<?php
/**
* 
*/
class WeChatPayFuns
{

	static function sign($pars,$wePaykey){    
		$pars['appkey']=$wePaykey;
	    ksort($pars);
	    $signString='';
		foreach ($pars as $key => $value) {
			$signString.=$key.'='.$value.'&';
		}
		$signString=substr($signString,0,-1);
		// echo $signString.'<br/>';
		$sign=sha1($signString);
		return $sign;
	}

/***
	productName 商品名
	orderID 商品id
	price 价格
*/
	static function creatPackage($productName,$orderID,$price){
		$notify=Utils::payNotifyUrl();
		$partnerId=C('wechatPayPartner');
		$partnerKey=C('wechatPayPartnerKey');
		$clinetIp=Utils::getClinetIp();

		$params = array();
		$params['bank_type']="WX";
		$params['body']=$productName;
		$params['fee_type']="1";
		$params['input_charset']="UTF-8";
		$params['notify_url']=$notify;
		$params['out_trade_no']=$orderID;
		$params['partner']=$partnerId;
		$params['spbill_create_ip']=$clinetIp;
		$params['total_fee']=$price;
		ksort($params);
		
		$url='';
		foreach ($params as $key => $value) {
			$url.=$key.'='.$value.'&';
		}
		$url=$url.'key='.$partnerKey;
		$packageSign=strtoupper(md5($url));

		$package='';
		foreach ($params as $key => $value) {
			$package.=$key.'='.urlencode($value).'&';
		}
		$package.="sign=$packageSign";
		return $package;
	}


	static function verifySign($pars,$wePaykey)
	{
		$tenpaySign=strtoupper($pars['sign']);
		unset($pars['sign']);
		$vars=array();
		foreach ($pars as $key => $value) {
			if ($value!='')$vars[$key]=$value;
		}
		ksort($vars);
		$url='';
		foreach ($vars as $key => $value) {
			$url.=$key.'='.$value.'&';
		}
		$sign=$url.'key='.$wePaykey;
		
		$sign=strtoupper(md5($sign));
		// echo $sign;
		return $sign == $tenpaySign;
	}

}


?>