<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_partner();
$partner_id = abs(intval($_SESSION['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);


           $where = " WHERE c.consume ='Y' AND tmp.partner_id ='$partner_id' AND tmp.state='pay' AND tmp.rstate='normal' AND tmp.is_bill = 0";
				
		    $sql = "SELECT tmp.id,tmp.js_price,tmp.quantity FROM".
			" (SELECT o.*,t.js_price FROM `order` AS o INNER JOIN".
			" `team` as t ON o.team_id = t.id) AS tmp".
			" INNER JOIN coupon AS c ON c.order_id = tmp.id ".$where;	
			
			$orders = DB::GetQueryResult($sql,false);
			
			$bill_price = 0;
			foreach($orders as $key=>$val){
			 $bill_price +=($val['js_price'])*$val['quantity'];
			}


$condition = array(
	'partner_id' => $partner_id,
);
$count = Table::Count('partner_bill', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);

$partner_bill = DB::LimitQuery('partner_bill', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));




include template('biz_bill_list');
