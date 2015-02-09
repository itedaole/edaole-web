<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();

$daytime = strtotime(date('Y-m-d'));
$nowtime = time();

$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);


$condition = array(
	'partner_id' => $partner_id,
);

/* end filter */
$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
));

$comment_list = array();
foreach($teams as $key=>$val){
    $comment_list[$key]['id']      = $val['id'];
	$comment_list[$key]['title']      = $val['title'];
	$comment_list[$key]['team_price']   = $val['team_price'];
	$comment_list[$key]['begin_time']      = $val['begin_time'];
	$comment_list[$key]['num']      = Table::Count('comment', array(
										    'partner_id' => $partner_id,
										    'team_id' => $val['id'],
									      )
                                        );
	$comment_list[$key]['fen'] = 	get_fen($val['id']);								
}


//echo '<pre>';
//print_r($comment_list);


function get_fen($id){
    $comment_list = array(); /*获取所有评论*/
	$comment_list = DB::LimitQuery('comment', array(
		'condition' => array(
			'team_id' => $id,
	   ),
	));
	foreach($comment_list as $val){
      $comment_num += $val['comment_grade'];
    }
	$all_comment = count($comment_list);
	return sprintf("%.1f", $comment_num/$all_comment); 
}
include template('mb_comment');


