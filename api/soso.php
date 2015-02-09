<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
header('Content-Type: application/xml; charset=GBK');

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
$city_id = abs(intval($city['id']));
$team = current_team($city_id);
$option_city = option_category('city');
$option_group = option_category('group');

$oa = array();
$oa['provider'] = $INI['system']['sitename'];
$oa['version'] = '1.0';
$oa['dataServiceId'] = '1_1';

$item = array();
$item['keyword'] = "{$INI['system']['sitename']} {$team['product']}";
$item['url'] = "{$INI['system']['wwwprefix']}/team.php?id={$team['id']}";
$item['creator'] = $_SERVER['HTTP_HOST'];
$item['title'] = "{$INI['system']['sitename']} {$team['product']}";
$item['publishdate'] = date('Y-m-d', $team['begin_time']);
$item['imageaddress1'] = team_image($team['image'], true);
$item['imagealt1'] = $team['title'];
$item['imagelink1'] = "{$INI['system']['wwwprefix']}/team.php?id={$team['id']}";
$item['content1'] = $team['product'];
$item['linktext1'] = $team['title'];
$item['linktarget1'] = "{$INI['system']['wwwprefix']}/team.php?id={$team['id']}";
$item['content2'] = "{$team['market_price']}元";
$item['content3'] = "{$team['team_price']}元";
$item['content4'] = team_discount($team)."折";
$item['content5'] = $option_group[$team['group_id']];
$item['content6'] = $city ? $city['name'] : '全国';
$item['content7'] = $team['now_number'];
$item['linktext2'] = $INI['system']['sitename'];
$item['linktarget2'] = $INI['system']['wwwprefix'];
$item['content8'] = date('Y-m-d H:i:s', $team['begin_time']);
$item['content9'] = date('Y-m-d H:i:s', $team['end_time']);
$item['valid'] = '1';

$oa['datalist']['item'] = $item;

Output::XmlCustom($oa, 'sdd', 'GBK');
