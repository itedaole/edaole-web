<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
$condition = array( 'user_id' => $login_user['id'],);
$count = Table::Count('flow', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$flows = DB::LimitQuery('flow', array(
			'condition'=>$condition,
			'size' => $pagesize,
			'offset' => $offset,
			'order' => 'ORDER BY id DESC',
			));

$detail_ids = Utility::GetColumn($flows, 'detail_id');
$teams = Table::Fetch('team', $detail_ids);
$users = Table::Fetch('user', $detail_ids);
$coupons = Table::Fetch('coupon', $detail_ids);

$pagetitle = '账户余额';
include template('credit_index');
