<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

	$token=urldecode($_GET['token']);
	$type=urlencode($_GET['type']);

	header('Content-Type:application/json; charset=utf-8');
	$res=array();
	$res['debug']['path']='/Runtime/Temp/'.$type.'_'.md5($token);
	
	$path=dirname(__FILE__).$res['debug']['path'];
	$res['debug']['type']=$type;
	
	$res['debug']['GET']=$_GET;
	$res['debug']['POST']=$_POST;

	if(!$token||(!file_exists($path)||is_dir($path))){//如果临时文件不存在
		$res['result']=false;
		$res['debug']['trace']='f1';
		$res['debug']['description']=_getAbsPath($path).' file not exist';
		$res['debug']['token']=$token;
	}else{
		if ($type=='sms') {//发短信
			$tell=trim(urldecode($_GET['tell']));
			$text=urldecode($_GET['text']);
			if(!$token||!file_exists($path)){
				$res['result']='系统异常,发送失败01';
				$res['debug']['trace']='f1';
			}else {
				$r=sms_send($tell,$text);
				$res['result']=$r;
				$res['debug']['result']=$r;
			}
		}else if($type=='pay'){//余额支付
			$order=$_POST['order'];
			$order=json_decode($order,true);
			$res['debug']['POST_ORDER']=$order;
			if ($order['id']) {
				addslashesss($order);
				$res['debug']['POST_ORDER_2']=$order;
				$user = Table::FetchForce('user', $order['user_id']);
				if ($user['money']<$order['origin']){
					$res['result']=false;
					$res['debug']['trace']='f2';
					$res['debug']['description']='money count error';
					$res['debug']['order']=$_POST['order'];
				}else{
					$ordernew = Table::FetchForce('order', $order['id']);
					if ($ordernew['state']=='pay') {
						$res['result']=false;
						$res['debug']['trace']='f3';
						$res['debug']['description']='order has payd';
						$res['debug']['order']=$_POST['order'];
					}else{
						Table::UpdateCache('order', $order['id'], array(
						'service' => 'credit',
						'money' => 0,
						'state' => 'pay',
						'credit' => $order['origin'],
						'pay_time' => time(),
						));
						$ordernew = Table::FetchForce('order', $order['id']);

						$res['debug']['result']=$ordernew;
						
						ZTeam::BuyOne($ordernew);
						$res['result']=true;
					}
				}
			}else{
				$res['result']=false;
				$res['debug']['trace']='f4';
				$res['debug']['description']='post order format error';
				$res['debug']['order']=$_POST['order'];
			}
		// $res['debug']=$order;
		// $res['path']=$path;
		}else if ($type=='pay3') {//第三方支付
			//获取参数
			$order=$_POST['order'];
			$order=json_decode($order,true);
			$res['debug']['POST_ORDER']=$order;
			if ($order['id']) {
				addslashesss($order);
				$orderid=$order['id'];//表id
				$payid=$order['payid'];//go-150-20-npms
				$money=$order['money'];//金额,元为单位
				$currency=$order['currency'];//货币，例如'CNY'
				$service=$order['service'];//支付类型：例如'tenpay'
				$bank=$order['bank'];//来源。例如 '财付通'
				$tradeno=$order['tradeno'];//订单号
				$res['debug']['POST_ORDER_2']=$order;
				//改变订单状态
				$res['result']=ZOrder::OnlineIt($orderid, $payid, $money, $currency, $service, $bank,$tradeno);
				$res['debug']['result']=$res['result'];
			}else{
				$res['result']=false;
				$res['debug']['trace']='f5';
			}
		}else if($type=='execCoupon'){//消费团购卷
			$cid=_iGet('code');
			$sec=_iGet('sec');
		 	$coupon = Table::FetchForce('coupon', $cid);
			$partner = Table::Fetch('partner', $coupon['partner_id']);
			$team = Table::Fetch('team', $coupon['team_id']);
			$check = (option_yes('mycoupon') || $coupon['user_id'] == $login_user_id || $coupon['partner_id'] == abs($_SESSION['partner_id']));
			$res['result']=false;
			$v = array();
			if (!$coupon) {
				$v[] = "#{$cid}&nbsp;无效";
				$v[] = '本次消费失败';
				$res['code']=401;
			}else if (false==$check) {
				$v[] = "#{$cid}&nbsp;无权消费";
				$v[] = '本次消费失败，请登录后操作';
				$res['code']=402;
			}else if ( !option_yes('onlycoupon') && $coupon['secret']!=$sec) {
				$v[] = $INI['system']['couponname'] . '编号密码不正确';
				$v[] = '本次消费失败';
				$res['code']=403;
			}else if ( $coupon['expire_time'] < strtotime(date('Y-m-d')) ) {
				$v[] = "#{$cid}&nbsp;已过期";
				$v[] = '过期时间：' . date('Y-m-d', $coupon['expire_time']);
				$v[] = '本次消费失败';
				$res['code']=404;
			}else if ( $coupon['consume'] == 'Y' ) {
				$v[] = "#{$cid}&nbsp;已消费";
				$v[] = '消费于：' . date('Y-m-d H:i:s', $coupon['consume_time']);
				$v[] = '本次消费失败';
				$res['code']=405;
			}else {
				ZCoupon::Consume($coupon);
		        if(option_yes('usecouponsms')) sms_usecoupon($coupon);
				//credit to user'money'
				$tip = ($coupon['credit']>0) ? " 返利{$coupon['credit']}元" : '';
				$v[] = $INI['system']['couponname'] . '有效';
				$v[] = '消费时间：' . date('Y-m-d H:i:s', time());
				$v[] = '本次消费成功' . $tip;
				$res['code']=200;
				$res['result']=true;
			}
			$res['context']=$v;

		}else if($type=='test'){//for autoTest
			$res['debug']['trace']='f6';
			@unlink($path);
			if (!file_exists($path)) {
				$res['delete']=true;
			}else{
				$res['delete']=false;
			}
			$res['result']=$res['delete'];
		}
	}
	if($res['result']){
		unset($res['debug']);	
	}else{
		$res['debug']['remote_ip']=_getClinetIp();
		$res['debug']['server_ip']=_getServerIp();
	}

	echo json_encode($res);
	
	if ($type!='test'||$res['result']) {
		@unlink($path);//清理临时文件	
	}
	


//---------------附加方法---------------
function addslashesss(&$arr){
	if (is_array($arr)) {
		foreach ($arr as $key => &$value) {
			addslashesss($arr[$key]);
		}
	}else{
		$arr=addslashes($arr);
	}
}

function _iGet($key,$decode=true,$trim=true){
	$v=$_GET[$key];
	if ($v&&$trim)$v=trim($v);
	if ($v&&$decode)$v=urldecode($v);
	return $v;
}

function _iPost($key,$decode=true,$trim=true){
	$v=$_POST[$key];
	if ($v&&$trim)$v=trim($v);
	if ($v&&$decode)$v=urldecode($v);
	return $v;
}

function _getAbsPath($path){
	list($_,$p)=explode('Runtime', $path);
	return $p?'/Runtime'.$p:false;
}

function _getServerIp(){
	$ip=$_SERVER['SERVER_ADDR'];
	return $ip?$ip:$_SERVER['LOCAL_ADDR'];
}

function _getClinetIp(){
	$user_IP = ($_SERVER["HTTP_VIA"]) ? $_SERVER["HTTP_X_FORWARDED_FOR"] : $_SERVER["REMOTE_ADDR"];
	$user_IP = ($user_IP) ? $user_IP : $_SERVER["REMOTE_ADDR"]; 
	return $user_IP;
}

?>



