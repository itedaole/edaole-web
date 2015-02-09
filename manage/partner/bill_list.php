<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
need_auth('market');



$condition = array();
$bill_sn = strval($_GET['bill_sn']);
$partner_id = strval($_GET['partner_id']);
$bill_status = strval($_GET['bill_status']);
if($bill_sn){
  $condition['bill_sn'] = $bill_sn;
}
if($partner_id){
  $condition['partner_id'] = $partner_id;
}
if($bill_status){
  $condition['bill_status'] = $bill_status;
}


$count = Table::Count('partner_bill', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);

$partner_bill = DB::LimitQuery('partner_bill', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));

$partner_ids = Utility::GetColumn($partner_bill, 'partner_id');
$partner = Table::Fetch('partner', $partner_ids);

$option_bill = array(
	'0' => '审核中',
	'1' => '已审核',
	'2' => '已拒绝',
);

include template('manage_partner_bill_list');
