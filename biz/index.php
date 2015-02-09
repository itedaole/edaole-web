<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_partner();
$partner_id = abs(intval($_SESSION['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);

$condition = array(
	'partner_id' => $partner_id,
);
$count = Table::Count('team', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10);

$teams = DB::LimitQuery('team', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));

$city_ids = Utility::GetColumn($teams, 'city_id');
$cities = Table::Fetch('category', $city_ids);


include template('biz_index');
