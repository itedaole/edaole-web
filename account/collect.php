<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
$condition = array( 
	 'user_id' => $login_user_id,	 
 );

$count = Table::Count('collect', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);
$orders = DB::LimitQuery('collect', array(
	'condition' => $condition,
	'order' => 'ORDER BY team_id DESC, id ASC',
	'size' => $pagesize,
	'offset' => $offset,
));

$pagetitle = '我的收藏';
include template('account_collect');
