<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$id = abs(intval($_GET['id']));
$quantity = abs(intval($_GET['quantity']));

$team = Table::Fetch('team', $id);


if ( $team['per_number']>0 && $quantity > $team['per_number'] ) {
   $quantity = $team['per_number'];
}


//whether buy
$ex_con = array(
		'user_id' => $login_user_id,
		'team_id' => $team['id'],
		'state' => 'unpay',
		);
$order = DB::LimitQuery('order', array(
	'condition' => $ex_con,
	'one' => true,
));

// //buyonce
// if (strtoupper($team['buyonce'])=='Y') {
// 	$ex_con['state'] = 'pay';
// 	if ( Table::Count('order', $ex_con) ) {

// 		Session::Set('error', '您已经成功购买了本单产品，请勿重复购买，快去关注一下其他产品吧！');
// 		redirect( WEB_ROOT . "/team.php?id={$id}"); 
// 	}
// }



//peruser buy count


if ( is_get() ) {
		Session::Set('loginpage', $_SERVER['REQUEST_URI']);
	}
//没有购买时走下面的流程
if(!$order) {
	need_login();
	$express_id = (int) $_POST['express_id'];

	if($team['delivery'] == 'express'){
		foreach ($express_ralate as $k=>$v) {
		$exp_id[] = $v['id'];
		$ex[$v['id']]['price'] = $v['price'];
     	}
		if (!in_array($express_id, $exp_id) && !empty($exp_id)) {
		   Session::Set('error', '非法请求');
		   redirect( WEB_ROOT . "/team.php?id={$team['id']}");
		}
		$express_price = abs($ex[$express_id]['price']);
	}	
	$condbuy = implode('@', $_POST['condbuy']);
	
	$table = new Table('order', $_POST);
	$table->quantity = abs(intval($table->quantity));


	if ($order && $order['state']=='unpay') {
		$table->SetPk('id', $order['id']);
	}

	$table->user_id = $login_user_id;
	$table->state = 'unpay';
	$table->allowrefund = $team['allowrefund'];
	$table->team_id = $team['id'];
	$table->city_id = $team['city_id'];
	$table->express = ($team['delivery']=='express') ? 'Y' : 'N';
	$table->fare = $table->express=='Y' ? $express_price : 0;
	$table->express_id = $table->express=='Y' ? $express_id : 0;
	$table->price = $team['team_price'];
	$table->partner_id = $team['partner_id'];
	$table->credit = 0;
	$table->condbuy = $condbuy;
	$table->source = '电脑付款';
	
	if ( $table->id ) {
		$eorder = Table::Fetch('order', $table->id);
		if ($eorder['state']=='unpay' 
				&& $eorder['team_id'] == $id
				&& $eorder['user_id'] == $login_user_id
		   ) {
			$table->origin = team_origin($team, $table->quantity,$express_price);
			$table->origin -= $eorder['card'];
		} else {
			$eorder = null;
		}
	}
	if (!$eorder){
		$table->pk_value='';
		$table->create_time = time();
		$table->origin = team_origin($team, $table->quantity,$express_price);
	}

	$insert = array(
			'user_id', 'team_id', 'city_id', 'partner_id','state', 'express_id',
			'fare', 'express_xx','express', 'origin', 'price',
			'address', 'zipcode', 'realname', 'mobile', 
			'quantity', 'create_time', 'remark', 'condbuy',
		);
	if($team['allowrefund']=='Y') $insert[] = 'allowrefund' ;
	if ($flag = $table->update($insert)) {
		$order_id = abs(intval($table->id));
		
		/* 插入订单来源 */
		$data['order_id'] = $order_id;
		$data['user_id'] = $login_user_id;
		$data['referer'] = $_COOKIE['referer'];
		$data['create_time'] = time();
		DB::Insert('referer', $data);
		
		//redirect(WEB_ROOT."/order/check.php?id={$order_id}");
	}
}

//each user per day per buy
if (!$order) { 
	$order = json_decode(Session::Get('loginpagepost'),true);
	settype($order, 'array');
	if ($order['mobile']) $login_user['mobile'] = $order['mobile'];
	if ($order['zipcode']) $login_user['zipcode'] = $order['zipcode'];
	if ($order['address']) $login_user['address'] = $order['address'];
	if ($order['realname']) $login_user['realname'] = $order['realname'];
	if ($team['permin_number']>1 && ($team['permin_number']<$team['per_number']|| $team['per_number']==0)){
        $order['quantity'] = $team['permin_number'];
	}else{
        $order['quantity'] = 1;
	}
	$order = Table::Fetch('order', $order_id);
}
//end;
$order['origin'] = team_origin($team, $order['quantity']);

if ($team['max_number']>0 && $team['conduser']=='N') {
	$left = $team['max_number'] - $team['now_number'];
	if ($team['per_number']>0) {
		$team['per_number'] = min($team['per_number'], $left);
	} else {
		$team['per_number'] = $left;
	}
}

$team['condbuy'] = explode('@', $team['condbuy']);

foreach ($team['condbuy'] as $k=>$v) {
	$team['condbuy'][$k] = nanooption($v);
}

/*订单总价*/
if($quantity > 1){
	$order['quantity'] = $quantity;
}
$partner = Table::Fetch('partner', $team['partner_id']);

ZCoupon::CheckOrder($order, true);

if (empty($login_user['couponcode'])) {
	$couponcode = ZUser::createCouponcode($login_user_id);
	$login_user['couponcode'] = $couponcode;
}

include template('team_buynew');
