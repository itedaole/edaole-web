<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();

$daytime = strtotime(date('Y-m-d'));
$nowtime = time();

$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);


/*已提款*/
$bill_price = get_price(1);
/*总额*/
$all_bill_price = get_price(3);
/*余额*/
$yue_price = get_price(0);


$condition = array(
	'partner_id' => $partner_id,
	'bill_status'=> 1
);

$partner_bill = DB::LimitQuery('partner_bill', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
));



function get_price($is_bill){
    $partner_id = abs(intval($_SESSION['partner_id']));

	if($is_bill ==3){
	    $where = " WHERE  tmp.partner_id ='$partner_id' AND tmp.state='pay' AND tmp.rstate='normal'";
	}else{

	    $where = " WHERE c.consume ='Y' AND tmp.partner_id ='$partner_id' AND tmp.state='pay' AND tmp.rstate='normal' AND tmp.is_bill = $is_bill";
	}
	
	
	
	
	$sql = "SELECT tmp.id,tmp.js_price,tmp.quantity FROM".
			" (SELECT o.*,t.js_price FROM `order` AS o INNER JOIN".
			" `team` as t ON o.team_id = t.id) AS tmp".
			" INNER JOIN coupon AS c ON c.order_id = tmp.id ".$where;

		
	$orders = DB::GetQueryResult($sql,false);
	$bill_price = 0;
	foreach($orders as $key=>$val){
	 $bill_price +=($val['js_price'])*$val['quantity'];
	}
	return $bill_price;
}





include template('mb_bill');




