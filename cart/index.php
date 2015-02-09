<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

session_start();
if(empty($_SESSION['mycart']['team_ids'])) die(include template('cart_none'));



/*购买了本团购的用户还看了*/
$now = time();
$condition_hai = array( 
		'team_type' => 'normal',
		"begin_time < '{$now}'",
		"end_time > '{$now}'",   
		);
	
$haiteams = DB::LimitQuery('team', array(
			'condition' => $condition_hai,
			'order' => 'ORDER BY rand()',
			'size'=>3,
));

//最近订单的收货地址
if($login_user_id){
	$address = DB::LimitQuery('order', array(
		'select'=>'realname,mobile,address',
		'condition' =>array('user_id'=>$login_user_id,'packageid'=>'-8','express'=>'Y',"`address`>'0' and `realname`>'0' and `mobile`>'0'"),
		'order'=>'order by create_time DESC',
		'one' => true,
	));
}

$now = time();
$condition = array(
	'id'=>$_SESSION['mycart']['team_ids'],
	"begin_time < '{$now}'",
	"end_time > '{$now}'",
	"(max_number=0 or (max_number>0 and max_number>now_number))",
);

$res= DB::LimitQuery('team', array(
		  'condition' => $condition,
		  ));
	  
$team_on=array();

foreach($res as $v)
{
 $team_on[$v['id']]=$v;
}

$team_ids=array();
$teams=array();
$total_price=0;
$total_num=0;

foreach($_SESSION['mycart']['teams'] as $k=>$v){
	if(!isset($team_on[$v['id']])){
		unset($_SESSION['mycart']['teams'][$k]);
	}else{
		$team_on[$v['id']]['num']=abs($v['num']);
		$team_on[$v['id']]['min_num']=max($team_on[$v['id']]['gmmin_number'],1);
		if($team_on[$v['id']]['per_number']>0&&$team_on[$v['id']]['max_number']>0){
			$team_on[$v['id']]['max_num']=min($team_on[$v['id']]['per_number'],($team_on[$v['id']]['max_number']-$team_on[$v['id']]['now_number']));
		}elseif($team_on[$v['id']]['per_number']>0){
			$team_on[$v['id']]['max_num']=$team_on[$v['id']]['per_number'];
		}elseif($team_on[$v['id']]['max_number']>0){
			$team_on[$v['id']]['max_num']=($team_on[$v['id']]['max_number']-$team_on[$v['id']]['now_number']);
		}else{
			$team_on[$v['id']]['max_num']=0;
		}

		
		$team_ids[]=$v['id'];
		$teams[$k]=$team_on[$v['id']];
		
		$total_price+=$team_on[$v['id']]['num']*$team_on[$v['id']]['team_price'];
		$total_num+=$team_on[$v['id']]['num'];
	}
}

if(empty($teams)) die(include template('cart_none'));
array_multisort($team_ids,SORT_DESC,$teams);

foreach($teams as $k=>$v){
	//是否不足量或超量
	if(is_post()){
		if($team_on[$v['id']]['num']<$v['min_num']||($v['max_num']>0&&$team_on[$v['id']]['num']>$v['max_num'])){
			Session::Set('error', "{$v['product']} 没有达到最少购买量或已超出最大购买量！");
			redirect( WEB_ROOT . "/cart/index.php");
		}
		
		
	}
	if ($v['delivery'] == 'express'){
	   $is_express = 1;
	}
	if($v['farefree']>0&&$team_on[$v['id']]['num']>=$v['farefree']){
		$p_teams[$v['partner_id']]['farefree']=1;	
	}
	$p_teams[$v['partner_id']]['fare']=$p_teams[$v['partner_id']]['farefree']?0:max($p_teams[$v['partner_id']]['fare'],$v['fare']);

	$p_teams[$v['partner_id']]['total_price']+=$v['num']*$v['team_price'];
	$p_teams[$v['partner_id']]['total_num']+=$v['num'];
	$p_teams[$v['partner_id']]['teams'][$k]=$v;
}


if($_POST){
    need_login();
	
	
	
		foreach($team_on as $v){
	          if (strtoupper($v['buyonce'])=='Y') {
					$ex_con['state'] = 'pay';
					if ( Table::Count('order', $ex_con) ) {

						Session::Set('error', '您已经成功购买了本单产品，请勿重复购买，快去关注一下其他产品吧！');
						redirect( WEB_ROOT . "/cart/index.php"); 
					}
				}
				
				//peruser buy count
					if ($v['per_number']>0) {
							$now_count = Table::Count('order', array(
								'user_id' => $login_user_id,
								'team_id' => $id,
								'state' => 'pay',
							), 'quantity');
							$v['per_number'] -= $now_count;
							if ($v['per_number']<=0) {	
								json('您购买本单产品的数量已经达到上限，快去关注一下其他产品吧！', 'alert');
							}
					}
	
	    }
	
	

	
	
	if(!$login_user_id){
		$login_user_id =Session::Get('no_user_id');
		if(!$login_user_id){
			$username ='匿名'.date('YmdHis',time()).rand(0,99);
			$exist_user = Table::Fetch('user', $username, 'username');
			while(!empty($exist_user)) {
				$username ='匿名'.date('YmdHis',time()).rand(0,99);
				$exist_user = Table::Fetch('user', $username, 'username');
			}
			$new_user = array(
				'username' => $username,
				'password' => rand(1000000,999999)
			);
			$login_user_id = ZUser::Create($new_user);
			Session::Set('no_user_id',$login_user_id);
		}
		
	}
	
	//计算购物车总价
	$hid_num = $_POST['hid_num'];
	foreach($team_on as $v){
	   $total_origin += $v['team_price']*$hid_num[$v['id']]['0'];
	}
	

	$order=array();
	$order['condbuy'] = implode('@', $_POST['condbuy']);
	if(abs(intval($_POST['address-list']))){
		$order['realname']=strval($_POST['o_realname']);
		$order['mobile']=strval($_POST['o_mobile']);
		$order['address']=strval($_POST['o_address']);
	}else{
		$order['realname']=strval($_POST['realname']);
		$order['mobile']=strval($_POST['mobile']);
		$order['address']=strval($_POST['province']. $_POST['area']. $_POST['city']. $_POST['street']);
	}
	$order['express_xx']=trim(strval($_POST['express_xx']))==''?'任何时间均可送货':trim(strval($_POST['express_xx']));
	$order['remark']=trim(strval($_POST['remark']))=='选填，可以告诉卖家您对商品的特殊要求，如快递、包装等'?'':trim(strval($_POST['remark']));
	$order['user_id']=$login_user_id;
	$order['express'] = 'Y';
	$order['create_time'] = time();
	$order['credit'] =0;
	$order['state'] = 'unpay';
	$order['city_id'] =abs($city['id']);
	$order['service'] ='tenpay';

	$insert = array(
			'realname', 'mobile', 'address', 'express_xx',
			'remark', 'user_id','express', 'create_time', 'credit',
		    'state', 'city_id','condbuy',
			'service', 'team_id', 'price', 'quantity','partner_id','fare','packageid','origin',
		);	
	
	foreach($team_on as $v){
		$new_order=$order;
		$new_order['team_id']=$v['id'];
		$new_order['price']=$v['team_price'];
		$new_order['quantity']=$hid_num[$v['id']]['0'];
		
		$new_order['partner_id']=$v['partner_id'];
		$new_order['fare']=$p_teams[$v['partner_id']]['fare'];
		
		$new_order['packageid']=$order_id>0?$order_id:-8;
		$new_order['origin']=$new_order['packageid']=='-8'?$total_origin:'0.00';

		$table = new Table('order', $new_order);
		if ($flag= $table->insert($insert)) {
			if($new_order['packageid']=='-8'){
				$order_id=$flag;
				unset($_SESSION['mycart']);
			}
		}else{
			Session::Set('error', '订单提交失败，请联系客服反馈！');
			redirect( WEB_ROOT . "/cart/index.php");
		}
	}
	redirect(WEB_ROOT."/order/check.php?id={$order_id}");
}
include template('cart_index');
