<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$city_id = $city['id'];

$daytime = time();
$condition_ev56_s = array( 
		"begin_time <  {$daytime}",
		"end_time > {$daytime}",
		);

$daytimenow_ev56_s = time();
/*判断预告，过期，秒杀，热销*/
$condition_ev56_s = array(
	"begin_time < $daytimenow_ev56_s ",
	"end_time > $daytimenow_ev56_s",
);


if($_GET['area']){
 $condition_ev56_s['area_id'] = $_GET['area'];
}



if($_GET['s']=="zk")
{
$order='ORDER BY sort_order DESC, market_price - team_price DESC, id DESC';
}
elseif($_GET['s']=="b")
{
$order='ORDER BY now_number DESC, id DESC';
}
elseif($_GET['s']=="jg")
{
$order='ORDER BY team_price DESC, id DESC';
}
else 
{
$order='ORDER BY sort_order DESC,begin_time DESC, id DESC';
}




/*echo $city_id; 判断城市*/
$condition_ev56_s['city_id'] = $city_id;


$group_id = abs(intval($_GET['gid']));
$fid_s = abs(intval($_GET['fid_s']));
/*大分类ID*/
if ($group_id<>0){$condition_ev56_s['group_id'] = $group_id;}
if ($fid_s<>0){$condition_ev56_s['sub_id'] = $fid_s;}


$size = !empty($size) ? $size : 30;

list($pagesize, $offset, $pagestring) = pagestring($count, $size,true);

$teams = DB::LimitQuery('team', array(
	'condition' => $condition_ev56_s,
	'order' => $order,
	'size' => $pagesize,
	'offset' => $offset,
));	 


if($_GET['act'] == "juli"){
	  $a = $_GET['la']; 
	  $b = $_GET['lo']; 
	  $teams = get_dinwei_team($teams,$a,$b);                  
}


/*区域列表*/
$area_list = DB::LimitQuery('category', array(
	'condition' => array(
		'zone' => 'area',
		'display' => 'Y',
		'fid'=>$city[id],
),
	'order' => 'ORDER BY sort_order DESC',
));


   
//获取二级分类

function get_two_category($fid){
	  $row = DB::LimitQuery('category', array(
		'condition' => array(
			'zone' => 'group',
			'display' => 'y',
			'fid'=> $fid,
		),
			'order' => 'ORDER BY sort_order DESC',
		));
	$category = array();
	
	foreach($row as $val){
	   $category[$val['id']]['id']    = $val['id'];
	   $category[$val['id']]['name']  = $val['name'];
	}
     return $category;	
}   

//获取一级分类
function get_one_category($gid='0') {
    global $city;
    $city_id = $city['id'];
	$today = strtotime(date('Y-m-d'));
	$condition_ev56_s_c = array(
		'team_type' => 'normal',
		"begin_time <= '$today'",
		"end_time > '$today'",
	);
    $condition_ev56_s_c[] = "(city_ids like '%@$city_id@%' or city_ids like '%@0@%') or (city_ids = '' and city_id in(0,$city_id))";
	$row = DB::LimitQuery('category', array(
		'condition' => array( 'zone' => 'group','fid' => '0','display' => 'Y' ),
		'order' => 'ORDER BY sort_order DESC, id DESC',
	));	

	$category = array();
	
	foreach($row as $val){
	   $category[$val['id']]['id']    = $val['id'];
	   $category[$val['id']]['name']  = $val['name'];
	   $category[$val['id']]['fid']   = get_two_category($val['id']);
	}
	return $category;
}

function get_num($gid){
	$daytime = time();
	$condition = array( 
			"begin_time <  {$daytime}",
			"end_time > {$daytime}",
			'group_id' => $gid,
			);
			
	$teams1 = DB::LimitQuery('team', array(
				'condition' => $condition,
				'order' => 'ORDER BY begin_time DESC, id DESC',
				));	

	return count($teams1);			
}

function get_num_f($gid){
	$daytime = time();
	$condition = array( 
			"begin_time <  {$daytime}",
			"end_time > {$daytime}",
			'sub_id"' => $gid,
			);
	$teams1 = DB::LimitQuery('team', array(
				'condition' => $condition,
				'order' => 'ORDER BY begin_time DESC, id DESC',
				));	
	return count($teams1);			
}		
			
$cate = get_one_category();

foreach($cate as $val){
   $all_num +=  get_num($val['id']);
}

$pagetitle = '团购分类';


function get_dinwei_team($teams,$latitude,$longitude){
     for($i = 0;$i < count($teams);$i++){
	     $partner = Table::Fetch('partner', $teams[$i]['partner_id']); //调用商家信息
		 $parr = explode(',',$partner['longlat']);
		 $juli = GetDistance($latitude,$longitude,$parr['0'],$parr['1']);
		 $teams[$i]['juli'] = round($juli*1000);
	     if($teams[$i]['juli'] > 1000){
	        $teams[$i]['julis'] = '距离 '.round($juli,1).'km';
		 }else{
		    $teams[$i]['julis'] = '距离 '.round($juli*1000).'m';
		 }
	 }
	 
	 $team = multi_array_sort($teams,'juli');
	 
	 return $team;
	 
}


function rad($d)  
{  
	   return $d * 3.1415926535898 / 180.0;  
}  
function GetDistance($lat1, $lng1, $lat2, $lng2)  
{  
	$EARTH_RADIUS = 6378.137;  
	$radLat1 = rad($lat1);  
	//echo $radLat1;  
   $radLat2 = rad($lat2);  
   $a = $radLat1 - $radLat2;  
   $b = rad($lng1) - rad($lng2);  
   $s = 2 * asin(sqrt(pow(sin($a/2),2) +  
	cos($radLat1)*cos($radLat2)*pow(sin($b/2),2)));  
   $s = $s *$EARTH_RADIUS;  
   $s = round($s * 10000) / 10000;  
   return $s;  
}  


function multi_array_sort($arr,$shortKey,$short=SORT_ASC,$shortType=SORT_REGULAR)
{
	foreach ($arr as $key => $data){
	$name[$key] = $data[$shortKey];
	}
	array_multisort($name,$shortType,$short,$arr);
	return $arr;
}


include template('m_category');
