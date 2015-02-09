<?php
require_once(dirname(__FILE__) . '/app.php');

$lat1 = abs(intval($_GET['lat']));
$lng1 = abs(intval($_GET['log']));

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

function get_team($partner_id){
    $now = time();
    $condition = array( 
		'team_type' => 'normal',
		"begin_time < '{$now}'",
		"end_time > '{$now}'",
		'partner_id'=>$partner_id,
	);
    $teams = DB::LimitQuery('team', array(
			'condition' => $condition,
			'order' => 'ORDER BY id DESC',
		));
		
	$team = array();
     foreach($teams as $key=>$val){
	     $team[$key]['id'] = $val['id'];
		 $team[$key]['title'] = $val['title'];
		 $team[$key]['team_price'] = $val['team_price'];
		 $team[$key]['image'] = 'http://'.$_SERVER['SERVER_NAME'].'/static/'.$val['image'];
	 }
		
	return $team;

}

$condition = array();
if (option_yes('citypartner') && ($cid=abs(intval($city['id']))) ) {
	$condition['city_id'] = $cid;
}

$partners_list = DB::LimitQuery('partner', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
));

foreach($partners_list as $key=>$val){
    $partners[$key]['pid'] = $val['id'];
	$partners[$key]['username'] = $val['username'];
	$partners[$key]['title'] = $val['title'];
	$partners[$key]['longlat'] = $val['longlat'];
    $partners[$key]['sublist'] = get_team($val['id']);
	$parr = explode(',',$val['longlat']);
	$partners[$key]['juli'] = GetDistance($lat1,$lng1,$parr['0'],$parr['1']);	
}


$partners = multi_array_sort($partners,'juli');

$partner_api = array();
$partner_api['status'] = 1;
$partner_api['list'] = $partners; 


echo json_encode($partner_api);


function multi_array_sort($arr,$shortKey,$short=SORT_ASC,$shortType=SORT_REGULAR)
{
	foreach ($arr as $key => $data){
	$name[$key] = $data[$shortKey];
	}
	array_multisort($name,$shortType,$short,$arr);
	return $arr;
}


