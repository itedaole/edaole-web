<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();


$comment_partner = DB::LimitQuery('comment_partner', array(
	'condition' => array(),
	'order' => 'ORDER BY id DESC',
));


include template('manage_comment_partner_index');


