<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$logos = DB::LimitQuery('friendlink', array(
			'condition' => array( 'LENGTH(logo)>0' ),
			'order' => 'ORDER BY sort_order DESC',
			));
$texts = DB::LimitQuery('friendlink', array(
			'condition' => array( 'LENGTH(logo)=0',),
			'order' => 'ORDER BY sort_order DESC',
			));

include template('help_link');
