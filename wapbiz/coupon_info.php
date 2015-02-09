<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();

$daytime = strtotime(date('Y-m-d'));
$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);

$id = $_GET['id'];

$coupon = Table::Fetch('coupon', $id);
$teams = Table::Fetch('team', $coupon['team_id']);
$users = Table::Fetch('user', $coupon['user_id']);

$pagetitle = '优惠卷详情';
include template('mb_coupon_info');
