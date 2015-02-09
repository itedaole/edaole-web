<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$city = array();
$daytime = strtotime(date('Y-m-d'));
$condition = array( 
	"begin_time <= {$daytime}", 
	"end_time > {$daytime}",
);

/* city filter */
$ename = strval($_GET['ename']);
if ($ename && $ename!='none') {
	$city = DB::LimitQuery('category', array(
		'condition' => array(
			'zone' => 'city',
			'ename' => $ename,
		),
		'one' => true,
	));
}
if ($ename||$city) {
	$city_id = abs(intval($city['id']));
	$condition[] = "((city_ids like '%@{$city_id}@%' or city_ids like '%@0@%') or city_id in(0,{$city_id}))";
}
/* end city filter */

$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => 'ORDER BY sort_order DESC, id DESC',
));

$oa = array();
$si = array(
		'site_name' => $INI['system']['sitename'],
		'site_title' => $INI['system']['sitetitle'],
		'site_url' => $INI['system']['wwwprefix'],
		);

foreach($teams AS $one) {
	$city = Table::Fetch('category', $one['city_id']);
	$group = Table::Fetch('category', $one['group_id']);
	team_state($one);
	$o = array();
	$o['id'] = $one['id'];
	$o['link'] = "{$si['site_url']}/team.php?id={$one['id']}";
	$o['large_image_url'] = team_image($one['image']);
	$o['small_image_url'] = team_image($one['image'], true);
	$o['title'] = $one['title'];
	$o['product'] = $one['product'];
	$o['team_price'] = $one['team_price'];
	$o['market_price'] = $one['market_price'];
	$o['rebate'] = team_discount($one);
	$o['start_date'] = date('c', $one['begin_time']);
	$o['end_date'] = date('c', $one['end_time']);
	$o['state'] = $one['state'];
	$o['tipped'] = ($one['reach_time'] > 0);
	$o['tipped_date'] = date('c', $one['reach_time']);
	$o['tipping_point'] = abs(intval($one['min_number']));
	$o['current_point'] = abs(intval($one['now_number']));

	$co = array();
	$co['limited_quantity'] = ($one['per_number']>0);
	$co['maximum_purchase'] = abs(intval($one['per_number']));
	$co['expiration_date'] = date('c', $one['expire_time']);
	$o['conditions'] = $co;

	$o['city'] = $city['name'];
	$o['group'] = $group['name'];
	$oa[$one['id']] = $o;
}
$o = array( 'site' => $si, 'teams' => $oa );
if ('json'===strtolower(strval($_GET['s']))) Output::Json($o);
header('Content-Type: application/xml; charset=UTF-8');
Output::Xml($o);
