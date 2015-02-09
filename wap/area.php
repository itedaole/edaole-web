<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$city_id = abs(intval($city['id']));
$group_id = abs(intval($_GET['gid']));



/*商圈*/

$shangquan = DB::LimitQuery('category', array(
	'condition' => array(
		'zone' => 'city',
		'display' => 'y',
		//'fid' => $city_id,
),
	'order' => 'ORDER BY sort_order DESC',
));


include template('wap_area');
