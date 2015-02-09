<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
//need_partner_wap();
$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);

$notice = '请输入团购卷编号';

if ( $_POST ) {
    $cid = trim($_POST['coupon']);
    $coupon = Table::FetchForce('coupon', $cid);
	$partner = Table::Fetch('partner', $coupon['partner_id']);
	
	$team = Table::Fetch('team', $coupon['team_id']);
	$check = (option_yes('mycoupon') || $coupon['user_id'] == $login_user_id || $coupon['partner_id'] == abs($_SESSION['partner_id']));
	if (!$coupon) {
		$notice = "#{$cid}&nbsp;无效";
		$v[] = '本次消费失败';
	}
	else if (false==$check) {
		$notice = "#{$cid}&nbsp;无权消费";
		$v[] = '本次消费失败，请登录后操作';
	}
	else if ( !option_yes('onlycoupon') && $coupon['secret']!=$sec) {
		$notice = $INI['system']['couponname'] . '编号密码不正确';
		$v[] = '本次消费失败';
	} else if ( $coupon['expire_time'] < strtotime(date('Y-m-d')) ) {
		$notice = "#{$cid}&nbsp;已过期";
		$v[] = '过期时间：' . date('Y-m-d', $coupon['expire_time']);
		$v[] = '本次消费失败';
	} else if ( $coupon['consume'] == 'Y' ) {
		$notice = "#{$cid}&nbsp;已消费";
		$v[] = '消费于：' . date('Y-m-d H:i:s', $coupon['consume_time']);
		$v[] = '本次消费失败';
	} else {
		ZCoupon::Consume($coupon);
        if(option_yes('usecouponsms')) sms_usecoupon($coupon);
		//credit to user'money'
		$tip = ($coupon['credit']>0) ? " 返利{$coupon['credit']}元" : '';
		$notice = $INI['system']['couponname'] . '有效';
		$v[] = '消费时间：' . date('Y-m-d H:i:s', time());
		$v[] = '本次消费成功' . $tip;
	}
}


$pagetitle = '消费登记';
include template('mb_validation');
