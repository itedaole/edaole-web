<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_manager();


if ($_POST) {
	$table = new Table('xh_topic', $_POST);
	$table->SetStrip('location', 'other');
	$table->create = time();
	$table->bgimage = upload_image('upload_bgimage', null, 'team');
	$table->image = upload_image('upload_image', null, 'team');
	$table->insert(array(
		'title', 'team_id', 'bgimage','image', 'display', 'is_index', 'create','sort_order'
	));
	redirect( WEB_ROOT . '/manage/topic/index.php');
}


include template('manage_topic_create');
