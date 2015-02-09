<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$daytime = time();
$condition = array( 
		
		"begin_time <  {$daytime}",
		"end_time > {$daytime}",
	
		);

/*Ray 搜索*/
$searchName = isset($_GET['searchName'])?trim($_GET['searchName']):'';
$condition[] = "( title like '%{$searchName}%' )";

$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10, true);
$teams = DB::LimitQuery('team', array(
			'condition' => $condition,
			'order' => 'ORDER BY id DESC, begin_time DESC',
			'size' => $pagesize,
			'offset' => $offset,
			));
foreach($teams AS $id=>$one){
	team_state($one);
	if ($one['state']=='none') $one['picclass'] = 'isopen';
	if ($one['state']=='soldout') $one['picclass'] = 'soldout';
	$teams[$id] = $one;
}

include template('m_search.html');
