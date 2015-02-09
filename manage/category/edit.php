<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
need_auth('admin');

$id = abs(intval($_REQUEST['id']));
$category = Table::Fetch('category', $id);
$table = new Table('category', $_POST);
$table->letter = strtoupper($table->letter);
$uarray = array( 'zone', 'ename', 'letter', 'name', 'czone','fid', 'display','is_hot', 'sort_order', 'relate_data'); 
$table->display = strtoupper($table->display)=='Y' ? 'Y' : 'N';

if (!$_POST['name'] || !$_POST['ename'] || !$_POST['letter']) {
	Session::Set('error', '中文名称、英文名称、首字母均不能为空');
	redirect(null);
}

if ( ! preg_match('/^([a-z]+)$/i', $_POST['ename']) 
		|| ! preg_match('/^([a-z]+)$/i', $_POST['letter']) ) {
	Session::Set('error', '英文名称及首字母均需应为字母');
	redirect(null);
}

if ( $category ) {
	if ( $flag = $table->update( $uarray ) ) {
		Session::Set('notice', '编辑分类成功');
	} else {
		Session::Set('error', '编辑分类失败');
	}
	option_category($category['zone'], true);
} else {
	if ( $flag = $table->insert( $uarray ) ) {
		Session::Set('notice', '新建分类成功');
	} else {
		Session::Set('error', '新建分类失败');
	}
}

option_category($table->zone, true);
redirect(null);
