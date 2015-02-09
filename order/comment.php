<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
need_login();
$order_id = abs(intval($_GET['id']));
$order = Table::Fetch('order', $order_id);
$team = Table::Fetch('team', $order['team_id']);
$cid = Table::Fetch('comment', $order_id,'order_id');
$group_id = $team['group_id'];

if($cid || !$order_id){
  redirect( WEB_ROOT . "/order/index.php");
}


/*查询星星标签*/
$comment_partner = DB::LimitQuery('comment_partner', array(
	'condition' => array("(cat_id like '%@{$group_id}@%')"),
	'order' => 'ORDER BY id DESC',
));


if ( is_post() ) {
	$comment = $_POST;
        //print_r($_POST);
		
		//echo $comment['xing_garde'] = json_encode($comment['xing']);
		//$xing_arr = json_decode($comment['xing_garde']);
		
		//print_r($xing_arr);
    $comment['comment_grade_all'] = json_encode($comment['xing']);

	$insert = array(
		'order_id', 'user_id','partner_id','team_id','add_time',
		'comment_content','image1','image2','image3','image4',
		'reply_cotent','comment_grade','comment_grade_all','comment_grade_three'
	);
	$comment['order_id'] = $order_id;
	$comment['user_id'] = $login_user_id;
	$comment['add_time'] = time();
	$comment['partner_id'] = abs(intval($team['partner_id']));
	$comment['team_id'] = abs(intval($order['team_id']));
	$comment['image1'] = upload_image('upload_image1',null,'team',true);
	$comment['image2'] = upload_image('upload_image2',null,'team');
	$comment['image3'] = upload_image('upload_image3',null,'team');
	$comment['image4'] = upload_image('upload_image4',null,'team');
	
	//print_r($comment);exit();
	
	$insert = array_unique($insert);
	$table = new Table('comment', $comment);
	
	if(!$cid){
	    if ( $table->insert($insert) ) {
		 Session::Set('notice', '评论成功');
		 redirect( WEB_ROOT . "/order/index.php");
		}
		else {
			Session::Set('error', '评论失败');
			redirect(null);
		}
	}

}	
$pagetitle = '用户评论';
include template('order_comment');

