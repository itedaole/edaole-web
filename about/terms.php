<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$page = Table::Fetch('page', 'about_terms');
$pagetitle = '用户协议';
include template('about_terms');
