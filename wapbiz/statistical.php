<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();

$daytime = strtotime(date('Y-m-d'));
$nowtime = time();

$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);


$condition = array(
	'partner_id' => $partner_id,
);

/* end filter */
$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
));






include template('mb_statistical');




