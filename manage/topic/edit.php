<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_manager();

$id = abs(intval($_GET['id']));
$topic = $etopic = Table::Fetch('xh_topic', $id);

if ( $_POST && $id==$_POST['id'] && $topic) {

	$table = new Table('xh_topic', $_POST);
	$table->bgimage = upload_image('upload_bgimage', $etopic['bgimage'], 'team');
    $table->image = upload_image('upload_image', $etopic['image'], 'team');

	$up_array = array(
			'title', 'team_id', 'bgimage','image', 'display', 'is_index', 'create','sort_order'
			);

	$flag = $table->update($up_array);
	
	if ($flag) {
		Session::Set('notice', '修改成功');
		redirect( WEB_ROOT . "/manage/topic/edit.php?id={$id}");
	}
	Session::Set('error', '修改失败');
}

include template('manage_topic_edit');
