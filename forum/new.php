<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
need_open(option_yes('navforum'));

$publics = option_category('public');

if ($_POST) {
	verify_captcha('verifytopic', WEB_ROOT . '/forum/new.php');
	$topic = new Table('topic', $_POST);
	if ( $topic->category == 'city' ) {
		$topic->city_id = abs(intval($city['id']));
	} else {
		$topic->public_id = abs(intval($topic->category));
	}

	$topic->user_id = $topic->last_user_id = $login_user_id;
	$topic->create_time = $topic->last_time = time();
	$topic->reply_number = 0;
	$insert = array(
			'user_id', 'city_id', 'public_id',
			'content', 'last_user_id', 'last_time',
			'reply_number',  'create_time', 'title',
			);
	if ( $topic_id = $topic->insert($insert) ) {
		redirect( WEB_ROOT . "/forum/topic.php?id={$topic_id}");
	}
	$topic = $_POST;
}

$id = abs(intval($_GET['id']));
$pagetitle = '发表新话题';
include template('forum_new');
