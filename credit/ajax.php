<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
if(!$login_user_id) json('请先登录','alert');
need_login();
$id = abs(intval($_GET['id']));
$action = strval($_GET['action']);

if ( $action == 'exchange') {
	$goods = Table::Fetch('goods', $id);   
	$now_count = Table::Count('credit', array(
				'user_id' => $login_user_id,
				'detail_id' => $goods['id'],
				'action' => 'exchange',
				));
	$leftnum = ($goods['per_number'] - $now_count);
	if ($leftnum <= 0) {
		json('您兑换的本商品数量已经达到上限', 'alert');
	}

	if ( $goods['consume'] >= $goods['number'] ) {
		json('本商品已兑换完毕', 'alert');
	}
	else if ( $goods['score'] > $login_user['score'] ) {
		json('你的积分余额不足，兑换失败', 'alert');
	}
	else if ( $goods['enable'] == 'N' ) {
		json('本商品暂时不参加兑换，请原谅', 'alert');
	}
	else{
		$html = render('ajax_dialog_fillhome');
		json($html, 'dialog');    
	}

	json('兑换失败', 'alert');
}
elseif ( 'edithome' == $action ) {   
	$goods = Table::Fetch('goods', $id); 
	$score = 0-$goods['score'];
	//兑换商品流程
	$user  = Table::Fetch('user', $login_user_id);
	Table::UpdateCache('user', $login_user_id, array(
				'score' => array( "`score`+{$score}" ),
				));    
	$u = array(
			'user_id' => $login_user_id,
			'admin_id' => 0,
			'detail_id' => $id,
			'score' => $score,
			'action' => 'exchange',
			'rname' => strval($_GET['n']),
			'rmobile' => strval($_GET['m']),
			'rcode' => strval($_GET['c']),
			'raddress' => strval($_GET['a']),
			'create_time' => time(),
		  );
	DB::Insert('credit', $u);
	Table::UpdateCache('goods', $id, array(
				'consume' => array( '`consume` + 1' ),
				));

	$v = "兑换商品[{$goods['title']}]成功，消耗积分{$goods['score']}";
	json( array(
				array( 'data' => $v, 'type' => 'alert'),
				array( 'data' => null,  'type' => 'refresh'),
		   ), 
			'mix');

}
