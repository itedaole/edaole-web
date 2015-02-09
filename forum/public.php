<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
need_open(option_yes('navforum'));

$publics = option_category('public');

$id = abs(intval($_GET['id']));
$condition = array( 'parent_id' => 0, );

if ( $id && $public = Table::Fetch('category', $id) ){ 
	$condition['public_id'] = $id;
} else if ($id) {
	redirect( WEB_ROOT . '/forum/public.php');	
} else {
	$condition[] = 'public_id > 0';
}

$count = Table::Count('topic', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);
$topics = DB::LimitQuery('topic', array(
			'condition' => $condition,
			'size' => $pagesize,
			'offset' => $offset,
			'order' => 'ORDER BY head DESC, last_time DESC',
			));
$user_ids = Utility::GetColumn($topics, 'user_id');
$luser_ids = Utility::GetColumn($topics, 'last_user_id');
$user_ids = array_merge($user_ids, $luser_ids);
$users = Table::Fetch('user', $user_ids);
$public = Table::Fetch('category', $id);

$pagetitle = $public ? "{$public['name']}讨论区" : '公共讨论区';
include template('forum_public');
