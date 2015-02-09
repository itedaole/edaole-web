<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$id = abs(intval($_GET['id']));

$order = Table::Fetch('order', $id);
if (!$order) { 
	Session::Set('error', '订单不存在！');
	redirect( 'index.php' );
}
$team = Table::Fetch('team', $order['team_id']);
$team['state'] = team_state($team);

if ( $team['close_time'] ) {
	redirect( "team.php?id={$id}");
}

/* generator unique pay_id */
if (! ($order['pay_id'] 
			&& (preg_match('#-(\d+)-(\d+)-#', $order['pay_id'], $m) 
				&& ( $m[1] == $order['id'] && $m[2] == $order['quantity']) )
	  )) {
	$randid = strtolower(Utility::GenSecret(4, Utility::CHAR_WORD));
	$pay_id = "go-{$order['id']}-{$order['quantity']}-{$randid}";
	Table::UpdateCache('order', $order['id'], array(
				'pay_id' => $pay_id,
				));
}
/* end */


include template('wap_check');
