<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$daytime = time();
$condition = array( 
		"begin_time <  {$daytime}",
		"end_time > {$daytime}",
		);

if(!empty($_GET['city'])){
  $city_id = abs(intval($_GET['city']));
  cookieset('city', $city_id);
  $city = Table::Fetch('category', $city_id);  
}else{
  $city_id = abs(intval($city['id']));
}		
		
/*echo $city_id; 判断城市*/
$condition['city_id'] = $city_id;


		
$size = $_GET['size'];	

$size = !empty($size) ? $size : 50;	
$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, $size, true);
$teams = DB::LimitQuery('team', array(
			'condition' => $condition,
			'order' => 'ORDER BY begin_time DESC, id DESC',
			'size' => $pagesize,
			'offset' => $offset,
			));
			

$pagetitle = $INI['system']['abbreviation'];


include template('m_index');
