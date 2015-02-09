<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$action = strval($_GET['a']);
$id = abs(intval($_GET['id']));
if('tocart' == $action) {
	session_start();
	$team = Table::Fetch('team', $id);
	if(!$team||$team['begin_time']>time()) json('商品不存在！', 'alert');
	if ( $team['close_time']||$team['end_time']<=time()) {
		json('该商品已下架，快去关注一下其他产品吧！', 'alert');
	}elseif($team['max_number']>0&&$team['max_number']<=$team['now_number']){
		json('该商品已卖光，快去关注一下其他产品吧！！', 'alert');
	}
	if ($team['team_type'] == 'iwant') {
		json('该商品未开始销售！', 'alert');
	}
	
if($login_user_id){	
 //每人限购
	If (strtoupper($team['buyonce'])=='Y') {
		$ex_con['state'] = 'pay';
		if ( Table::Count('order', $ex_con) ) {
		
				json('您已经成功购买了本单产品，请勿重复购买，快去关注一下其他产品吧！', 'alert');

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
				json('您购买本单产品的数量已经达到上限，快去关注一下其他产品吧！', 'alert');
			}
		}
		else {
			if ($team['max_number']>0) $team['per_number'] = $team['max_number'] - $team['now_number'];
		}
	 
	 
	 
	 
	 
}
	/*
	if($team['delivery']!='express') 
	{
	  json("window.location=WEB_ROOT+'/team/buy.php?id=".$id."'",'eval');
	}
	*/
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
	
	

	$condbuy=array();

	if(empty($condbuy)){
		$key=md5($id);
		$_SESSION['mycart']['teams'][$key]=array('id'=>$id,'num'=>max($team['gmmin_number'],1));
		if(!in_array($id,$_SESSION['mycart']['team_ids']))
        { 		
		  $_SESSION['mycart']['team_ids'][]=$id;
		}

		$html = render('ajax_dialog_carttips');		
		json($html, 'dialog');
	}

}elseif('delcart' == $action) {
	session_start();
	$key=trim(strval($_GET['key']));
	$keys=explode(',',$key);
	if(empty($keys)) json('0', 'eval');
	foreach($keys as $v) {
		unset($_SESSION['mycart']['teams'][$v]);
	}
	$_SESSION['mycart']['team_ids']=array();
	foreach($_SESSION['mycart']['teams'] as $v){
		$_SESSION['mycart']['team_ids'][]=$v['id'];
	}
	$_SESSION['mycart']['team_ids']=array_unique($_SESSION['mycart']['team_ids']);
	if(empty($_SESSION['mycart']['team_ids'])){
		json('2', 'eval');
	}
	json('1', 'eval');
}

?>
