<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
need_auth('admin');

$action = strval($_GET['action']);
$id = abs(intval($_GET['id']));

$r = udecode($_GET['r']);
$tid = strval($_GET['tid']);
$like = strval($_GET['like']);


$condition = array();
if ($tid) { $condition['team_id'] = $tid; }
if ($cate) { $condition['comment_grade'] = $cate; }
if ($like) { 
	$condition[] = "comment_content like '%".mysql_escape_string($like)."%'";
}

$count = Table::Count('comment', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$orders = DB::LimitQuery('comment', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));

$team_ids = Utility::GetColumn($orders, 'team_id');
$teams = Table::Fetch('team', $team_ids);

$user_ids = Utility::GetColumn($orders, 'user_id');
$users = Table::Fetch('user', $user_ids);

include template('manage_misc_comment');
