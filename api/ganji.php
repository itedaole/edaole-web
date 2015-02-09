<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
header('Content-Type: application/xml; charset=UTF-8');

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
$groups = option_category('group', false, false);

$oa = array();
$si = array(
		'sitename' => $INI['system']['sitename'],
		'wwwprefix' => $INI['system']['wwwprefix'],
		'imgprefix' => $INI['system']['imgprefix'],
		);

foreach($teams AS $one) {
	$city = Table::Fetch('category', $one['city_id']);
	$group = Table::Fetch('category', $one['group_id']);
	$item = array();
	$item['loc'] = $si['wwwprefix'] . "/team.php?id={$one['id']}";
	$item['data'] = array();
	$item['data']['display'] = array();

	$o = array();
	$o['website'] = $INI['system']['sitename'];
	$o['siteurl'] = $INI['system']['wwwprefix'];
	($o['city'] = $city['name']) || ($o['city'] = '全国');
	$o['title'] = $one['title'];
	$o['class'] = $groups[$one['group_id']];
	$o['image'] = $si['imgprefix']  .'/static/' . $one['image'];
	$o['startTime'] = $one['begin_time'];
	$o['endTime'] = $one['end_time'];
	$o['value'] = $one['market_price'];
	$o['price'] = $one['team_price'];
	if ( $one['market_price'] > 0 ) {
		$o['rebate'] = moneyit(10*$one['team_price']/$one['market_price']);
	} else {
		$o['rebate'] = '0';
	}
	$o['bought'] = abs(intval($one['now_number']));

	$item['data']['display'] = $o;
	$oa[] = $item;
}

Output::XmlBaidu($oa);
