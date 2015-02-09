<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_manager();
$id = abs(intval($_GET['id']));
$action = $_GET['action'];

if (!$id) {
	json('广告不存在', 'alert');
}

if ( $action == 'adremove') {

    Table::Delete('adinfo', $id);

	Session::Set('notice', '删除成功');
	json(null, 'refresh');
}
elseif ( $action == 'adoff' ) {
	Table::UpdateCache('adinfo', $id, array( 'available' => 0,));
	$tip = '关闭成功';
	Session::Set('notice', $tip);
	json(null, 'refresh');
}elseif( $action == 'adon'){
   	Table::UpdateCache('adinfo', $id, array( 'available' => 1,));
	$tip = '开始成功';
	Session::Set('notice', $tip);
	json(null, 'refresh');
}
