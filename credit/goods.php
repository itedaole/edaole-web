<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login();
$condition = array();
if (!option_yes('usercredit')) {
    Session::Set('notice', "未开启积分模块！");
	redirect(WEB_ROOT . '/order/index.php');
  }
$count = Table::Count('goods', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$goods = DB::LimitQuery('goods', array(
			'condition'=>$condition,
			'size' => $pagesize,
			'offset' => $offset,
			'order' => 'ORDER BY id DESC',
			));

$pagetitle = '兑换商品';
include template('credit_goods');
