<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

need_login(true);

$daytime = strtotime(date('Y-m-d'));
$condition = array(
	'user_id' => $login_user_id,
);

/* filter */
$selector = strval($_GET['s']);
if( $selector == 'expired' ) {
	$condition['consume'] = 'N';
	$condition[] = "expire_time < {$daytime}";
	$suffix = '_expired';
}
elseif ( $selector == 'consumed' ) {
	$condition['consume'] = 'Y';
	$suffix = '_consumed';
}
else {
	$condition['consume'] = 'N';
	$condition[] = "expire_time >= {$daytime}";
}
/* end filter */

$count = Table::Count('coupon', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 10, true);
$coupons = DB::LimitQuery('coupon', array(
	'condition' => $condition,
	'order' => 'ORDER BY team_id DESC, create_time DESC',
	'size' => $pagesize,
	'offset' => $offset,
));

$team_ids = Utility::GetColumn($coupons, 'team_id');
$teams = Table::Fetch('team', $team_ids);

include template("m_mycoupon");
