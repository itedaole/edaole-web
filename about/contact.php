<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$page = Table::Fetch('page', 'about_contact');
$pagetitle = '联系方式';
include template('about_contact');
