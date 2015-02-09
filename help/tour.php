<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$page = Table::Fetch('page', 'help_tour');
$pagetitle = '玩转' . $INI['system']['abbreviation'];
include template('help_tour');
