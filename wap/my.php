<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
need_login(true);

$consume_times = Table::Count('order', array(
			'user_id' => $login_user_id,
			'state' => 'pay',
			));

die(include template('m_my'));
