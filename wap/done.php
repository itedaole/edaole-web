<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$daytime = time();
$condition = array( 
		'city_id' => array(0, abs(intval($city['id']))),
		"begin_time <  {$daytime}",
		'OR' => array(
			"now_number >= min_number",
			"end_time > {$daytime}",
			),
		);

$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 5, true);
$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => 'ORDER BY begin_time DESC, id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));
foreach($teams AS $id=>$one){
	team_state($one);
	if ($one['state']=='none') $one['picclass'] = 'isopen';
	if ($one['state']=='soldout') $one['picclass'] = 'soldout';
	$teams[$id] = $one;
}

include template('wap_done');
