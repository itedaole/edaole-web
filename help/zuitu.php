<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$page = Table::Fetch('page', 'help_zuitu');
$pagetitle = $INI['system']['abbreviation'] . '是什么';
include template('help_zuitu');
