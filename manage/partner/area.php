<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();
need_auth('market');

$fid = abs(intval($_GET["fid"]));
$zone = htmlspecialchars(trim($_GET['zone']));
$czone=array('area'=>'区域','circle'=>'商圈');

if(empty($fid)){
	die('<option value="">选择'.$czone[$zone].'</option>');
}
$condition = array(
	'zone' => $zone,
	'fid'=>$fid
);

$areas = DB::LimitQuery('category', array(
	'condition' => $condition,
	'order' => 'ORDER BY sort_order',
));
if(!$areas) die('<option value="0">暂无'.$czone[$zone].'</option>');
$areas = Utility::OptionArray($areas, 'id', 'name');
die ("<option value=''>选择{$czone[$zone]}</option>".Utility::Option($areas));


