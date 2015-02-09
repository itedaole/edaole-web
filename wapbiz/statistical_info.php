<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

//need_partner_wap();

$daytime = strtotime(date('Y-m-d'));
$nowtime = time();
$id = $_GET['id'];
$partner_id = $_GET['partner_id'];

$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);
$team = Table::Fetch('team', $id);

if($team['partner_id'] != $partner_id){
    redirect( WEB_ROOT . "/wapbiz/statistical.php");
}

$no_xiaofei = $team['now_number'] - $team['coupon_number'];

$condition = array(
	'partner_id' => $partner_id,
	'team_id'=>$id,
	'consume'=>'Y',
);

/* end filter */
$coupon = DB::LimitQuery('coupon', array(
	'condition' => $condition,
	'order' => 'ORDER BY consume_time DESC ,id DESC',
));
$coup = Array();
foreach($coupon as $key=>$val){
   $coup[$key]['today'] = date('Y-m-d',$val['consume_time']);
}

$sum = array();

foreach($coup as $arr){
	if(!empty($arr)){
		foreach($arr as $key =>  $value){
			if($sum[$key][$value]){
				$sum[$key][$value]++;
			}else{
				$sum[$key][$value] = 1;
			}
		}
	}
}

foreach($sum as $key => $value){
	$i = 0;
	if(!empty($value)){
		foreach($value as $k => $v){
		    $sum_c[$k]['i'] = $i;
			$sum_c[$k]['m'] = $k;
			$sum_c[$k]['n'] = $v; 
			$i++;
		}
	}
	
}



include template('mb_statistical_info');




