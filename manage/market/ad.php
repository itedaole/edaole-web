<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
need_auth('market');

$condition = array();

/* filter */
/* filter end */

$count = Table::Count('adinfo', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$ads = DB::LimitQuery('adinfo', array(
	'condition' => $condition,
	'order' => 'ORDER BY type DESC, available DESC,displayorder DESC,id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));

$pagetypes = array('header'=>'页面顶部','footer'=>'页脚','sidetop'=>'右侧栏顶部','sidebottom'=>'右侧栏底部',);
$availables = array('1'=>'是','0'=>'否');

include template("manage_market_ad");

function current_market_ad($s=null) {
	$filter = array(
		'ad' => '广告列表',
		'adedit' => '添加广告',
	);
	$a['/manage/market/ad.php'] = '广告列表';
	foreach($filter AS $id=>$name) {
		$a["/manage/market/{$id}.php"] = $name;
	}
	$l = '/manage/market/ad.php';
	if ($s) $l = "/manage/market/{$s}.php";
	return current_link($l, $a, true);
}
