<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();


$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);
$id = $_GET['id'];
$day = $_GET['d'];
$daytime = strtotime(date($day));

$condition = array(
	'partner_id' => $partner_id,
	'team_id'=>$id,
	'consume'=>'Y',
);
$condition[] = "consume_time >= $daytime AND consume_time < $daytime+86400";

/* end filter */
$coupon = DB::LimitQuery('coupon', array(
	'condition' => $condition,
	'order' => 'ORDER BY consume_time DESC ,id DESC',
));



include template('mb_coupon');
