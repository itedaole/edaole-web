<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login(true);

$order_id = $id = strval(intval($_GET['id']));
if(!$order_id || !($order = Table::Fetch('order', $order_id))) {
	die('404 Not Found');
}
if ( $order['user_id'] != $login_user['id']) {
	redirect( "team.php?id={$order['team_id']}");
}
if ( $order['state']=='unpay') {
	redirect( "team.php?id={$order['team_id']}");
}

$team = Table::FetchForce('team', $order['team_id']);
$partner = Table::Fetch('partner', $order['partner_id']);
$express = ($team['delivery']=='express');
if ( $express ) { $option_express = Utility::OptionArray(Table::Fetch('category', array('express'), 'zone'), 'id', 'name'); }

if ( $team['delivery'] == 'coupon' ) {
	$cc = array(
			'user_id' => $login_user['id'],
			'team_id' => $order['team_id'],
			);
	$coupons = DB::LimitQuery('coupon', array(
				'condition' => $cc,
				));
}
function jpush($a,$b,$c,$d,$e) 
{ 
  $opts = array(  
			'http'=>array(
			'method'=>"GET",    
			'timeout'=>2,   
			)
			);
		$context = stream_context_create($opts);
	    $aaa= Table::Fetch('user',$c,'id');
		$date_time_array = getdate($a); //1311177600  1316865566
        $hours = $date_time_array["hours"];
        $minutes = $date_time_array["minutes"];
        $ss='http://www.tuan0598.com/jpushapi/index.php?a='.iconv("UTF-8","GB2312",$aaa['username'].'Android¹º'.$d.'·Ý,'.$hours.':'.$minutes).'&b='.iconv("UTF-8","GB2312","Phone".$e.'-'.$b);
	   file_get_contents($ss, false, $context);  
    
}
include template('wap_order_json');
