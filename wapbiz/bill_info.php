<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();

$daytime = strtotime(date('Y-m-d'));
$nowtime = time();

$id = abs(intval($_GET['id']));
$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);

$bill = Table::Fetch('partner_bill', $id);

//print_r($partner_bill);

include template('mb_bill_info');




