<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_partner();

$daytime = strtotime(date('Y-m-d'));
$partner_id = abs(intval($_SESSION['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);


$title = strval($_GET['title']);
$grad = strval($_GET['grad']);
$condition = $t_con = array(
	'partner_id' => $partner_id,
);
/* stat num*/
$nowtime = time();
$condition = array( 
			'partner_id' => $partner_id,   
		);

/* filter */
if ($title) { 
	$t_con[] = "title like '%".mysql_escape_string($title)."%'";
	$teams = DB::LimitQuery('team', array(
				'condition' => $t_con,
				));
	$team_ids = Utility::GetColumn($teams, 'id');
	if ( $team_ids ) {
		$condition['team_id'] = $team_ids;
	} else { $title = null; }
}
if($_GET['act'] == 'reply'){
  $condition['reply_cotent'] = '';
}
if ($grad) {
	switch(strtolower($grad)) {
		case 'good': $condition['comment_grade_three'] = '3'; break;
		case 'none': $condition['comment_grade_three'] = '2'; break;
		case 'bad' : $condition['comment_grade_three'] = '1';  default;
	}
}

/* end filter */

$count = Table::Count('comment', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);

$orders = DB::LimitQuery('comment', array(
	'condition' => $condition,
	'order' => 'ORDER BY team_id DESC, id ASC',
	'size' => $pagesize,
	'offset' => $offset,
));
$team_ids = Utility::GetColumn($orders, 'team_id');
$teams = Table::Fetch('team', $team_ids);

$user_ids = Utility::GetColumn($orders, 'user_id');
$users = Table::Fetch('user', $user_ids);
 
$option_grad = array(
	'good' => '好评',
	'none' => '中评',
	'bad' => '差评',
);

include template('biz_comment');
