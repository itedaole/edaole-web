<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$id = abs(intval($_GET['id']));
$comp = Table::Fetch('comment_partner', $id);


$condition = array(
	'zone' => 'group',
	'display' => 'Y',
);

$cate = DB::LimitQuery('category', array(
	'condition' => $condition,
	'order' => 'ORDER BY sort_order DESC, id DESC',
));




if ( is_post() ){
   $comp = $_POST;
   $comp['cat_id'] = '@'.implode('@', $comp['cat_id']).'@';
   
   $insert = array('name', 'cat_id');
   
   $insert = array_unique($insert);
   $table = new Table('comment_partner', $comp);
   
   if ( $comp['id'] && $comp['id'] == $id ) {
		$table->SetPk('id', $id);
		$table->update($insert);
		log_admin('comment_partner', '编辑星星',$insert);
		Session::Set('notice', '编辑信息成功');
		redirect( WEB_ROOT . "/manage/comment/comment_partner_index.php");
	} 
	else if ( $comp['id'] ) {
		log_admin('comment_partner', '非法编辑',$insert);
		Session::Set('error', '非法编辑');
		redirect( WEB_ROOT . "/manage/comment/comment_partner_index.php");
	}

	if ( $table->insert($insert) ) {
		log_admin('comment_partner', '新建',$insert);
		Session::Set('notice', '新建成功');
		redirect( WEB_ROOT . "/manage/comment/comment_partner_index.php");
	}
	else {
		log_admin('comment_partner', '编辑失败',$insert);
		Session::Set('error', '编辑失败');
		redirect(null);
	}

}






include template('manage_comment_partner_edit');


