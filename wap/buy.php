<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login(true);

$id = abs(intval($_GET['id']));

$team = Table::Fetch('team', $id);
if ( !$team || $team['begin_time']>time() ) {
	Session::Set('error', '团购项目不存在');
	redirect( 'index.php' );
}
team_state($team);
if ($team['close_time']) {
	Session::Set('error', '团购项目已结束，不能再次购买');
	redirect( 'index.php' );
}
/* 查询快递清单 */
if ($team['delivery'] == 'express') {
	$express_ralate = unserialize($team['express_relate']);
	foreach ($express_ralate as $k=>$v) {
		$express[$k] = Table::Fetch('category',$v['id']);
		$express[$k]['relate_data'] = $v['price'];
	}
}

/* 查询用户收货地址*/
if ($team['delivery'] == 'express') {
	$c = array('user_id'=>$login_user_id);
	$address = DB::LimitQuery('address',array(
		'condition' => $c,
		'order' => 'ORDER BY id DESC',
	));
	$def = DB::LimitQuery('address',array(
		'condition' => array('user_id'=>$login_user_id,'default'=>'Y'),
	));
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

//buyonce
if (strtoupper($team['buyonce'])=='Y') {
	$ex_con['state'] = 'pay';
	if ( Table::Count('order', $ex_con) ) {
		Session::Set('error', '您已经成功购买了本单产品，请勿重复购买，快去关注一下其他产品吧！');
		redirect( "index.php"); 
	}
}

//peruser buy count
if ($team['per_number']>0) {
	$now_count = Table::Count('order', array(
		'user_id' => $login_user_id,
		'team_id' => $id,
		'state' => 'pay',
	), 'quantity');
	$team['per_number'] -= $now_count;
	if ($team['per_number']<=0) {
		Session::Set('error', '您购买本单产品的数量已经达到上限，快去关注一下其他产品吧！');
		redirect( 'index.php' ); 
	}
}

//post buy
if ( $_POST ) {
	need_login(true);
	

	$express_id = (int) $_POST['express_id'];
	/*
	if($team['delivery'] == 'express'){
		foreach ($express_ralate as $k=>$v) {
		$exp_id[] = $v['id'];
		$ex[$v['id']]['price'] = $v['price'];
     	}
		if (!in_array($express_id, $exp_id) && !empty($exp_id)) {
		   Session::Set('error', '非法请求');
		   redirect( WEB_ROOT . "/team/buy.php?id={$team['id']}");
		}
		$express_price = abs($ex[$express_id]['price']);
	}*/
	$table = new Table('order', $_POST);
	
	
	$table->quantity = abs(intval($table->quantity));

	if ( $table->quantity == 0 ) {
		Session::Set('error', '购买数量不能小于1份');
		redirect( "buy.php?id={$team['id']}");
	} 
	elseif ( $team['per_number']>0 && $table->quantity > $team['per_number'] ) {
		Session::Set('error', '您本次购买本单产品已超出限额！');
		redirect( "buy.php?id={$team['id']}"); 
	}


	if ($order && $order['state']=='unpay') {
		$table->id = $order['id'];
	}

	$table->user_id = $login_user_id;
	$table->team_id = $team['id'];
	$table->city_id = $team['city_id'];
	$table->express = ($team['delivery']=='express') ? 'Y' : 'N';
	$table->express_id = $table->express=='Y' ? $express_id : 0;
	$table->price = $team['team_price'];
	$table->credit = 0;
	$table->state = 'unpay';
	if ( $table->id ) {
			$eorder = Table::Fetch('order', $table->id);
			if ($eorder['state']=='unpay' && $eorder['team_id'] == $id	&& $eorder['user_id'] == $login_user_id ) {
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
	
	if ($team['delivery']=='express') {
	    if(empty($_POST['realname'])){
		   	Session::Set('error', '收货人不能为空');
			Session::Set('loginpagepost', json_encode($_POST));
			redirect("buy.php?id={$team['id']}");
		}
		if(empty($_POST['address'])){
		   	Session::Set('error', '收货地址不能为空');
			Session::Set('loginpagepost', json_encode($_POST));
			redirect("buy.php?id={$team['id']}");
		}
	}
			
	if(empty($_POST['mobile'])){
		Session::Set('error', '手机不能为空');
		Session::Set('loginpagepost', json_encode($_POST));
		redirect("buy.php?id={$team['id']}");
	}
	
/*
	if ($team['delivery']=='express') {
		if (!$table->address 
			|| !Utility::IsMobile($table->mobile)
			|| !$table->zipcode
			|| !$table->realname
			) {
			Session::Set('error', '购买选项填写不完整');
			Session::Set('loginpagepost', json_encode($_POST));
			redirect("buy.php?id={$team['id']}");
		}
	}
*/

	$insert = array(
			'service','user_id', 'team_id', 'city_id', 'state','express_id', 
			'fare', 'express','express_xx', 'origin', 'price',
			'address', 'zipcode', 'realname', 'mobile', 'quantity',
			'create_time', 'remark',
		);
	

	if ($flag = $table->update($insert)) {

		$order_id = abs(intval($table->id));
		/* 插入订单来源 */
		$data['order_id'] = $order_id;
		$data['user_id'] = $login_user_id;
		$data['referer'] = $_COOKIE['referer'];
		$data['create_time'] = time();	
		
		DB::Insert('referer', $data);
		
		Session::Set('notice', '提交成功');
		redirect("pay.php?id={$order_id}");
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
	$order['quantity'] = 1;
}
//end;

$order['origin'] = team_origin($team, $order['quantity'],$express_price);

if ($team['max_number']>0 && $team['conduser']=='N') {
	$left = $team['max_number'] - $team['now_number'];
	if ($team['per_number']>0) {
		$team['per_number'] = min($team['per_number'], $left);
	} else {
		$team['per_number'] = $left;
	}
}

 
include template('m_buy');
