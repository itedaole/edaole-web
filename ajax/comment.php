<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_partner();
$action = strval($_GET['action']);
$id = $order_id = abs(intval($_GET['id']));
$comment = Table::Fetch('comment', $id);


if ( 'comment' == $action) {
	$comment = Table::Fetch('comment', $id);
	$html = render('manage_ajax_dialog_comment');
	json($html, 'dialog');
}
else if( 'editcomment' == $action) {
		
		$u = array(
		'reply_cotent' => strval($_GET['t']),
	   );
	Table::UpdateCache('comment', $id, $u);
	
		json( array(
				array('data'=>'回复成功', 'type' => 'alert',),
				array('data'=>'X.boxClose();', 'type' => 'eval',),
				array('data'=>'null', 'type' => 'refresh',),
			   ), 'mix');
}
