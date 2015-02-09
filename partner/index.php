<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$daytime = time();
$condition = array( 
		'open' => 'Y',
		);
$gid = $group_id = abs(intval($_GET['gid']));
$aid = abs(intval($_GET['aid']));
$sc = trim($_GET['sc']);
$keywords = trim($_GET['keywords']);


if($keywords){
  $condition[] = "( title like '%".mysql_escape_string($keywords)."%' )";
}


if ($group_id) $condition['group_id'] = $group_id;

if ($aid) $condition['area_id'] = $aid;

if (option_yes('citypartner') && ($cid=abs(intval($city['id']))) ) {
	$condition['city_id'] = $cid;
}

if($sc == 'sell'){
 $order = "ORDER BY (comment_good + comment_none + comment_bad) DESC, id DESC";
}else if($sc == 'new'){ 
 $order = "ORDER BY create_time DESC, id DESC";
}else if($sc == 'rete'){
 $order = "ORDER BY comment_good DESC, id DESC";
}
else{
 $order = "ORDER BY head DESC, id DESC";
}


$count = Table::Count('partner', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);
$partners = DB::LimitQuery('partner', array(
	'condition' => $condition,
	'order' => $order,
	'size' => $pagesize,
	'offset' => $offset,
));
foreach($partners AS $id=>$one){
	team_state($one);
	if ($one['state']=='none') $one['picclass'] = 'isopen';
	if ($one['state']=='soldout') $one['picclass'] = 'soldout';
	$one['comment_num'] = ($one['comment_good']+$one['comment_bad']+$one['comment_none']);
	$one['reputation'] = ($one['comment_num']>0)? moneyit(number_format(100*($one['comment_good']/$one['comment_num']), 2)) : null;
	$partners[$id] = $one;
}

/* now_comments */
$now_cc = array(
	'state' => 'pay',
	'comment_display' => 'Y',
	'comment_time > 0',
	'partner_id > 0',
);
$now_comments = DB::LimitQuery('order', array(
	'condition' => $now_cc,
	'order' => 'ORDER BY comment_time DESC',
));
foreach($now_comments AS $k=>$v) {
		$v['grade'] = 'A';
		$v['grade'] = $v['comment_grade']=='none' ? 'P' : $v['grade'];
		$v['grade'] = $v['comment_grade']=='bad' ? 'F' : $v['grade'];
		$v['comment'] = htmlspecialchars($v['comment_content']);
		$v['timespan'] = $daytime - $v['comment_time'];
		$now_comments[$k] = $v;
}

$partner_ids = Utility::GetColumn($now_comments, 'partner_id');
$cpartners = Table::Fetch('partner', $partner_ids);

$user_ids = Utility::GetColumn($now_comments, 'user_id');
$users = Table::Fetch('partner', $user_ids);

/* end */



/*今日团购项目*/
$now = time();
$condition_day = array( 
			'team_type' => 'normal',
			"begin_time < '{$now}'",
			"end_time > '{$now}'",
	);
$teams_day = DB::LimitQuery('team', array(
			'condition' => $condition_day,
			'order' => 'ORDER BY begin_time ASC, `sort_order` DESC, `id` DESC',
			'size' => 10,
  )); 
  
/*商家分类*/
$partner_cat = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'partner',
		  'display'=>'Y',
		  ), 
		'order' => 'ORDER BY display ,sort_order DESC, id DESC',
));
$cat_partner = array();
foreach($partner_cat as $key=>$val){
        $cat_partner[$key]['name'] = $val[name];
		$cat_partner[$key]['ename'] = $val[ename];
		$cat_partner[$key]['id'] = $val[id];
		$cat_partner[$key]['number'] = Table::Count('partner', array('group_id'=>$val[id],'city_id'=>$cid,'display'=>'Y'));
}
//地区分类
$area = DB::LimitQuery('category', array('condition'=>array(
		  'zone'=>'area', 
		  'fid'=>$city[id], 
		  'display'=>'Y',
		  ), 
		'order' => 'ORDER BY display ,sort_order DESC, id DESC',
));
for($i = 0;$i<count($area);$i++){
  $area[$i]['number'] = Table::Count('partner', array('group_id'=>$group_id,'area_id'=>$area[$i][id],'display'=>'Y'));
}


$category = Table::Fetch('category', $group_id);
$pagetitle = '品牌商户';
include template('partner_index');


//项目数量
function get_team_num($id){
   $now = time();
   $condition = array( 
			'team_type' => 'normal',
			"begin_time < '{$now}'",
			"end_time > '{$now}'",
			"partner_id"=>$id
	);
$teams = DB::LimitQuery('team', array(
			'condition' => $condition,
  )); 
  
  return count($teams);
}
//最近消费数量
function get_coupon_num($id){
       $now = time() - 2592000;
       $condition = array( 
			"consume_time > '{$now}'",
			"partner_id"=>$id
	);
$coupon = DB::LimitQuery('coupon', array(
			'condition' => $condition,
  )); 
  
  return count($coupon);
}
//类别
function get_group_name($id){

  $cates = Table::Fetch('category', $id); 

  return $cates['name'];
}


