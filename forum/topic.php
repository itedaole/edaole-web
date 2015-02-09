<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
need_open(option_yes('navforum'));

$id = abs(intval($_GET['id']));
if (!$id ||!$topic = Table::Fetch('topic', $id)){
	redirect( WEB_ROOT . '/forum/index.php' );
}
if ($topic['parent_id']>0) {
	redirect( WEB_ROOT . "/forum/topic.php?id={$topic['parent_id']}");
}

if (is_post()) {
	need_login();
	$reply = new Table('topic', $_POST);
	$reply->user_id = $login_user_id;
	$reply->city_id = $topic['city_id'];
	$reply->team_id = $topic['team_id'];
	$reply->public_id = $topic['public_id'];
	$reply->create_time = time();
	$insert =  array(
			'user_id', 'parent_id', 'content', 'create_time',
			'city_id', 'team_id', 'public_id',
		);
	$reply->insert($insert);

	$count = Table::Count('topic', array('parent_id' => $id));
	Table::UpdateCache('topic', $topic['id'], array(
		'reply_number' => $count,
		'last_time' => $reply->create_time,
		'last_user_id' => $login_user_id,
	));
	Session::Set('once_notice', '发表回复成功');
	redirect( WEB_ROOT . "/forum/topic.php?id={$id}");
}

$pagetitle = "{$topic['title']} 讨论区";
$count = $topic['reply_number'];
list($pagesize, $offset, $pagestring) = pagestring($count, 10);

$replies = DB::LimitQuery('topic', array(
	'condition' => array( 'parent_id' => $id, ),
	'order' => 'ORDER BY ID ASC',
	'size' => $pagesize,
	'offset' => $offset,
));
$user_ids = Utility::GetColumn($replies, 'user_id');
$user_ids[] = $topic['user_id'];
$users = Table::Fetch('user', $user_ids);

$public = Table::Fetch('category', $topic['public_id']);
Table::UpdateCache('topic', $id, array('view_number' => array('view_number + 1')));

include template('forum_topic');
