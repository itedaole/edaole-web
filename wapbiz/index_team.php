<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);

$daytime = time();
$condition = array( 
		"begin_time <  {$daytime}",
		"end_time > {$daytime}",
		"partner_id"=>$partner_id,
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

$teams = DB::LimitQuery('team', array(
			'condition' => $condition,
			'order' => 'ORDER BY begin_time DESC, id DESC',
			));
			

$pagetitle = $INI['system']['abbreviation'];


include template('mb_team');
