<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$action = isset($_GET['consume']) ? 'consume' : null;
$action = isset($_GET['query']) ? 'query' : $action;

$cid = strval($_GET['id']);
$sec = strval($_GET['secret']);

if($action == 'query') {
	$coupon = Table::FetchForce('coupon', $cid);
	$partner = Table::Fetch('partner', $coupon['partner_id']);
	$team = Table::Fetch('team', $coupon['team_id']);
	$e = date('Y-m-d', $team['expire_time']);

	if (!$coupon) { 
		$v[] = "#{$cid}&nbsp;无效";
	} else if ( $coupon['consume'] == 'Y' ) {
		$v[] = $INI['system']['couponname'] . '无效';
		$v[] = '消费于：' . date('Y-m-d H:i:s', $coupon['consume_time']);
	} else if ( $coupon['expire_time'] < strtotime(date('Y-m-d')) ) {
		$v[] = "#{$cid}&nbsp;已过期";
		$v[] = '过期日期：' . date('Y-m-d', $coupon['expire_time']);
	} else {
		$v[] = "#{$cid}&nbsp;有效";
		$v[] = "{$team['title']}";
		$v[] = "有效期至&nbsp;{$e}";
	}
	$v = join('<br/>', $v);
}

else if($action == 'consume') {
	$coupon = Table::FetchForce('coupon', $cid);
	$partner = Table::Fetch('partner', $coupon['partner_id']);
	$team = Table::Fetch('team', $coupon['team_id']);

	if (!$coupon) {
		$v[] = "#{$cid}&nbsp;无效";
		$v[] = '本次消费失败';
	}
	else if ($coupon['secret']!=$sec) {
		$v[] = $INI['system']['couponname'] . '编号密码不正确';
		$v[] = '本次消费失败';
	} else if ( $coupon['expire_time'] < strtotime(date('Y-m-d')) ) {
		$v[] = "#{$cid}&nbsp;已过期";
		$v[] = '过期时间：' . date('Y-m-d', $coupon['consume_time']);
		$v[] = '本次消费失败';
	} else if ( $coupon['consume'] == 'Y' ) {
		$v[] = "#{$cid}&nbsp;已消费";
		$v[] = '消费于：' . date('Y-m-d H:i:s', $coupon['consume_time']);
		$v[] = '本次消费失败';
	} else {
		ZCoupon::Consume($coupon);
		//credit to user'money'
		$tip = ($coupon['credit']>0) ? " 返利{$coupon['credit']}元" : '';
		$v[] = $INI['system']['couponname'] . '有效';
		$v[] = '消费时间：' . date('Y-m-d H:i:s', time());
		$v[] = '本次消费成功' . $tip;
	}
	$v = join('<br/>', $v);
}
if($v) Session::Set('notice', $v);
die(include template('wap_verify'));
