<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
require_once(dirname(dirname(__FILE__)) . '/order/paybank.php');

need_login();

$money = abs(intval($_GET['money']));
if (!$money) $money = '';

/* payservice choice */
if(@$INI[$order['service']]['mid'] || @$INI[$order['service']]['acc']) {
	$ordercheck[$order['service']] = 'checked';
}
else {
	foreach($payservice AS $pone) {
		if(@$INI[$order['service']]['mid'] || @$INI[$order['service']]['acc']) { $ordercheck[$order['service']] = 'checked'; }
	}
}


$pagetitle = '在线充值';
include template('credit_charge');
