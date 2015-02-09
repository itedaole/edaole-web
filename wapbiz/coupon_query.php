<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
//need_partner_wap();
$partner_id = abs(intval($_GET['partner_id']));
$login_partner = Table::Fetch('partner', $partner_id);




