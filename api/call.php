<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$action = strval($_GET['action']);
$callerid = strval($_GET['callerid']);
$cid = strval($_GET['num']);
$sec = strval($_GET['secret']);
$allow = array('query','consume');
if (false==in_array($action, $allow))  redirect(WEB_ROOT . '/index.php');

header('Content-Type: application/xml; charset=UTF-8');
//优惠券查询
if($action == 'query') {
	$coupon = Table::FetchForce('coupon', $cid);
	$partner = Table::Fetch('partner', $coupon['partner_id']);
	$team = Table::Fetch('team', $coupon['team_id']);
	$e = date('Y-m-d', $team['expire_time']);

	if (!$coupon) { 
        $arr = array(
			result=> '0',
            id=> ' ',
			product=> ' ',
			price=> ' ',
 		);
	} else if ( $coupon['consume'] == 'Y' ) {
	    $arr= array(
		   result=> '1',
		   id=> $coupon['team_id'],
		   product=> $team['product'],
	   	   price=> $team['team_price'],
		);
	} else if ( $coupon['expire_time'] < strtotime(date('Y-m-d')) ) {
		$arr= array(
		   result=> '2',
		   id=> $coupon['team_id'],
		   product=> $team['product'],
	   	   price=> $team['team_price'],
		);
	} else {
		$arr= array(
		   result=> '3',
		   id=> $coupon['team_id'],
		   product=> $team['product'],
	   	   price=> $team['team_price'],
		);
	}
	Output::XmlCustom($arr,'coupon');
}
//优惠券消费
else if($action == 'consume') {
	$coupon = Table::FetchForce('coupon', $cid);
	$partner = Table::Fetch('partner', $coupon['partner_id']);
	$team = Table::Fetch('team', $coupon['team_id']);

	if (!$coupon) {
		$result = false;
	}
	else if ($coupon['secret']!=$sec) {
		$result = false;
	} else if ( $coupon['expire_time'] < strtotime(date('Y-m-d')) ) {
		$result = false;
	} else if ( $coupon['consume'] == 'Y' ) {
		$result = false;
	} else {
		ZCoupon::Consume($coupon);
        if(option_yes('usecouponsms')) sms_usecoupon($coupon);
		$result = true;
		$arr=array(
            result=> true,
			product => $team['product'],
		    price => $team['team_price'],
	    );
	}
	if ($result) {
		Output::XmlCustom($arr,'coupon');
	} else {
		Output::XmlCustom(array(result=>'false'),'coupon');
	}
}
if($_GET['act'] == 'zhouda_pass'){ 
  	editzhpasses('zdapasses');
}