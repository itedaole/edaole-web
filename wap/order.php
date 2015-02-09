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

include template('m_order');
