<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$id = abs(intval($_GET['id']));
if (!$id || !$team = Table::FetchForce('team', $id) ) {
	redirect('index.php');
}
/*xxk*/
$ll = $partner['longlat'];
if ($ll) list($lati,$longi) = preg_split('/[,\s]+/',$ll,-1,PREG_SPLIT_NO_EMPTY);

$discount_price = $team['market_price'] - $team['team_price'];

$left = array();
$now = time();
$diff_time = $left_time = $team['end_time']-$now;

$left_day = floor($diff_time/86400);
$left_time = $left_time % 86400;
$left_hour = floor($left_time/3600);
$left_time = $left_time % 3600;
$left_minute = floor($left_time/60);
$left_time = $left_time % 60;
$partner = Table::Fetch('partner', $team['partner_id']);
team_state($team);
include template('wap_detail');
