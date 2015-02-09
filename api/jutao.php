<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
header('Content-Type: application/xml; charset=UTF-8');

$daytime = strtotime(date('Y-m-d'));
$condition = array( 
		'team_type' => 'normal',
		"begin_time <= {$daytime}",
		"end_time > {$daytime}",
		);
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
	$item['data']['companys']['company']= array();

	$o = array();
	$o['website'] = $INI['system']['sitename'];
	$o['siteurl'] = $INI['system']['wwwprefix'];
	($o['city'] = $city) || ($o['city'] = '');
	$o['title'] = $one['title'];
	$o['image'] = $si['imgprefix']  .'/static/' . $one['image'];

	$o['soldout'] = (($team['now_number'] > $team['max_num']) && ($team['max_num'] > 0)) ? 'yes' : 'no';
	$o['buyer'] = abs(intval($one['now_number']));
	$o['start_date'] = $one['begin_time'];

	$o['end_date'] = $one['end_time'];
	$o['expire_date'] = $one['expire_time'];
	$o['oriprice'] = $one['market_price'];
	$o['curprice'] = $one['team_price'];
	if ( $one['market_price'] > 0 ) {
		$o['discount'] = moneyit(10*$one['team_price']/$one['market_price']);
	} else {
		$o['discount'] = '0';
	}


	$o['tip'] = $one['notice'];

	$item['data']['display'] = $o;


	$p = Table::Fetch('partner', $one['partner_id']);
	$pval = array();
	$pval['name'] = $p['title'];
	$pval['contact'] = $p['tel'];
	$pval['address'] = $p['address'];
	if ($p['longlat']) {
		list($pval['longitude'], $pval['latitude']) = explode(',', $p['longlat']);
	}
	$item['data']['companys']['company']=$pval;
	$oa[] = $item;
}

Output::XmlBaidu($oa);
