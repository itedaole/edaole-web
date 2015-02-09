<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
$condition = array( 'user_id' => $login_user['id'],'action'=>'exchange');

if (!option_yes('usercredit')) {
    Session::Set('notice', "未开启积分模块！");
	redirect(WEB_ROOT . '/order/index.php');
  }
$count = Table::Count('credit', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$credits = DB::LimitQuery('credit', array(
			'condition'=>$condition,
			'size' => $pagesize,
			'offset' => $offset,
			'order' => 'ORDER BY id DESC',
			));
//print_r($credits);
//exit;
$detail_ids = Utility::GetColumn($credits, 'detail_id');

$pagetitle = '兑换记录';
include template('credit_records');
