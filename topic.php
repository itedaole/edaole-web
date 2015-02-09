<?php
require_once(dirname(__FILE__) . '/app.php');

$id = abs(intval($_GET['id']));
if (!$id || !$topic = Table::FetchForce('xh_topic', $id) ) {
	redirect( WEB_ROOT . '/index.php');
}
$pagetitle = $topic['title'];

$daytime = time();
$condition = array( 
		"id in ({$topic['team_id']})",     
		);
	
$oldteams = DB::LimitQuery('team', array(
			'condition' => $condition,
			'order' => 'ORDER BY  sort_order  DESC, id DESC',
			));

$teams=array();
$teamarr=explode(",",$topic['team_id']);
$indexteam=0;
foreach($teamarr as $inteamid)
{
     foreach($oldteams as $oldteam)
     {
         if($oldteam['id']==$inteamid)
         {
            $teams[$indexteam]=$oldteam;
            $indexteam=$indexteam+1;
         }
     }
}


include template('topic');

