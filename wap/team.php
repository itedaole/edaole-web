<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$id = abs(intval($_GET['id']));
if (!$id || !$team = Table::FetchForce('team', $id) ) {
	redirect('index.php');
}

$discount_price = $team['market_price'] - $team['team_price'];

$partner = Table::Fetch('partner', $team['partner_id']); //调用商家信息

$team['partner_title'] = $partner['title'];
$team['partner_phone'] = $partner['phone'];
$team['partner_address'] = $partner['address'];
$team['longlat'] = $partner['longlat'];
if ($team['longlat']) list($longi,$lati) = preg_split('/[,\s]+/',$team['longlat'],-1,PREG_SPLIT_NO_EMPTY);


$left = array();
$now = time();
$diff_time = $left_time = $team['end_time']-$now;

$left_day = floor($diff_time/86400);
$left_time = $left_time % 86400;
$left_hour = floor($left_time/3600);
$left_time = $left_time % 3600;
$left_minute = floor($left_time/60);
$left_time = $left_time % 60;

team_state($team);

$pagetitle = '团购详情';

include template('m_team_view');
