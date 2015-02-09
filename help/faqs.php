<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$page = Table::Fetch('page', 'help_faqs');
$pagetitle = '常见问题';
include template('help_faqs');
