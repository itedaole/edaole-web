<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_partner();
$partner_id = abs(intval($_SESSION['partner_id']));
$id = abs(intval($_GET['id']));
$login_partner = Table::Fetch('partner', $partner_id);
$action = strval($_GET['action']);

if ( 'bill' == $action) {

           $where = " WHERE c.consume ='Y' AND tmp.partner_id ='$partner_id' AND tmp.state='pay' AND tmp.rstate='normal' AND tmp.is_bill = 0";
				
		    $sql = "SELECT tmp.id,tmp.js_price,tmp.quantity FROM".
			" (SELECT o.*,t.js_price FROM `order` AS o INNER JOIN".
			" `team` as t ON o.team_id = t.id) AS tmp".
			" INNER JOIN coupon AS c ON c.order_id = tmp.id ".$where;		
				
		 	//json($sql, 'alert');	
				
		 
		 $orders = DB::GetQueryResult($sql,false);
		 $bill_price = 0;
		 foreach($orders as $key=>$val){
		     $bill_price +=($val['js_price'])*$val['quantity'];
			   /* order update */
				Table::UpdateCache('order', $val['id'], array(
							'is_bill' => 1,
			    ));
		 }
		 if(!$bill_price){
		     json('申请账单成金额不足', 'alert');
		 }
         		 
		$insert = array(
				'partner_id',
				'bill_sn',
				'add_time',
				'price',
				'bill_status',
				'bank_sn',
				'bank_name',
				'name'
		);
		$bill_arr['partner_id'] = $partner_id;
		$bill_arr['bill_sn'] = date('ymdHi');
		$bill_arr['add_time'] = time();
		$bill_arr['price'] = $bill_price;
		$bill_arr['bill_status'] = 0;
		$bill_arr['bank_sn'] = $login_partner['bank_no'];
		$bill_arr['bank_name'] = $login_partner['bank_name'];
		$bill_arr['name'] = $login_partner['bank_user'];
		$table = new Table('partner_bill', $bill_arr);
		//插入
		if ( $table->insert($insert) ) {
			Session::Set('notice', "申请账单成功");
     	    json(null, 'refresh');
		}
		else {
			Session::Set('error', '申请账单失败');
			redirect(null);
		}

}
if ( 'billdetail' == $action) {
     $bill = Table::Fetch('partner_bill', $id);
	 $html = render('biz_ajax_dialog_billdetail');
	 json($html, 'dialog');
}

