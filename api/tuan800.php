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

$oa = array();
$si = array(
		'sitename' => $INI['system']['sitename'],
		'wwwprefix' => $INI['system']['wwwprefix'],
		'imgprefix' => $INI['system']['imgprefix'],
		);
$cities = option_category('city');
$groups = option_category('group');

foreach($teams AS $one) {
	$city = $cities[$one['city_id']];
	$group = $groups[$one['group_id']];
	$item = array();
	$item['loc'] = $si['wwwprefix'] . "/team.php?id={$one['id']}";
	$item['data'] = array();
	$item['data']['display'] = array();
	$item['data']['shops']['shop']= array();

	$o = array();
	$o['website'] = $INI['system']['sitename'];
	$o['identifier'] = $one['id'];
	$o['siteurl'] = $INI['system']['wwwprefix'];
	($o['city'] = $city) || ($o['city'] = '全国');
	($o['tag'] = $group) || ($o['tag'] = '无');
	$o['title'] = $one['title'];
	$o['image'] = team_image($one['image'], true);
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
	$o['maxQuota'] = $one['max_number'];
	$o['minQuota'] = $one['min_number'];
	$o['post'] = ($one['delivery'] == 'express') ? 'yes' : 'no';
	$o['soldOut'] = (($one['now_number'] > $one['max_num']) && ($one['max_num'] > 0)) ? 'yes' : 'no';
	$o['tip'] = $one['notice'];

	$item['data']['display'] = $o;


	$p = Table::Fetch('partner', $one['partner_id']);
	$pval = array();
	$pval['name'] = $p['title'];
	$pval['tel'] = $p['tel'];
	$pval['address'] = $p['address'];
	if ($p['longlat']) {
		list($pval['longitude'], $pval['latitude']) 
			= explode(',', $p['longlat']);
	}
	$item['data']['shops']['shop']=$pval;
	$oa[] = $item;
}

Output::XmlBaidu($oa);
