<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$page = Table::Fetch('page', 'about_job');
$pagetitle = '加入我们';
include template('about_job');
