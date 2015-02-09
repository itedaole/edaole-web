<?php
require_once(dirname(__FILE__) . '/app.php');
$city_id = abs(intval($city['id']));
$gid = abs(intval($_GET['gid']));
$sid = abs(intval($_GET['sid']));
$aid = abs(intval($_GET['aid']));
$sc  = $_GET['sc'];
$a  = $_GET['a'];
$min_price  = $_GET['min_price'];
$max_price  = $_GET['max_price'];
$keywords = trim($_GET['keywords']);

$now = time();

$condition = array( 
		'team_type' => 'normal',
		"begin_time < '{$now}'",
		"end_time > '{$now}'",
		'city_id'=>$city_id,
);

if($gid){
  $condition['group_id'] = $gid;
}
if($sid){
  $condition['sub_id'] = $sid;
}
if($aid){
  $condition['area_id'] = $aid;
}


if($keywords){
  $condition[] = "( title like '%".mysql_escape_string($keywords)."%' )";
}



if($gid){
   $cates = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'group', 
		  'fid'=>$gid, 
		  'display'=>'Y',
		  ), 
		  'order'=>'ORDER BY `sort_order` DESC, `id` DESC'
	));
	for($i = 0;$i<count($cates);$i++){
	  $cates[$i]['number'] = Table::Count('team', array('team_type'=>'normal', 'sub_id'=>$cates[$i][id], "begin_time < '{$now}'", "end_time > '{$now}'"));
	}
}else{
	 $cates = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'group', 
		  'fid'=>0, 
		  'display'=>'Y',
		  ), 
		  'order'=>'ORDER BY `sort_order` DESC, `id` DESC'
	));
    for($i = 0;$i<count($cates);$i++){
	  $cates[$i]['number'] = Table::Count('team', array('team_type'=>'normal', 'group_id'=>$cates[$i][id], "begin_time < '{$now}'", "end_time > '{$now}'"));
	}
}

$area = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'area', 
		  'fid'=>$city[id], 
		  'display'=>'Y',
		  ), 
		'order' => 'ORDER BY display ,sort_order DESC, id DESC',
	));
for($i = 0;$i<count($area);$i++){
  $area[$i]['number'] = Table::Count('team', array('team_type'=>'normal', 'group_id'=>$gid,'sub_id'=>$sid,'area_id'=>$area[$i][id], "begin_time < '{$now}'", "end_time > '{$now}'"));
}


if($sc == 'xl'){
   $order='ORDER BY now_number DESC, id DESC';
}else if($sc == 'zhekou'){
   $order='ORDER BY market_price - team_price DESC, id DESC';
}else if($sc =='price'){
   $order='ORDER BY team_price DESC, id DESC';
}else if($sc == 'new'){
   $order='ORDER BY begin_time DESC, sort_order DESC,id DESC';
}else if($a == 'new'){
    $order ='ORDER BY begin_time ASC, `sort_order` DESC, `id` DESC';
}else{
   $order='ORDER BY `sort_order` DESC, `id` DESC';
}

if($min_price && $max_price){
   $condition[] = " ( team_price >= $min_price and team_price <= $max_price )"; 
}
if($_GET['wifi']){
   $condition['wifi'] = 'Y';
}
if($_GET['$park']){
  $condition['park'] = 'Y';
}
if($_GET['$holiday']){
  $condition['holiday'] = 'Y';
}
if($_GET['$free_yuyue']){
  $condition['free_yuyue'] = 'Y';
}
if($_GET['$weekend']){
  $condition['weekend'] = 'Y';
}


$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 36);
$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => $order,
	'size' => $pagesize,
	'offset' => $offset,
));

/*热门团购*/

$hot_cat = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'group',
		  'is_hot'=>'Y', 
		  'display'=>'Y',
		  'fid <> 0',
		  ), 
		'order' => 'ORDER BY display ,sort_order DESC, id DESC',
		'size' => 10,
));

include template('category');


