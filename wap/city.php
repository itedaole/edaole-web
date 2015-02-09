<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');

$ename = strval($_GET['n']);
$currefer = null;
if ($ename&&$ename!='none') {
	$city = Table::Fetch('category', $ename, 'ename');
	if ($city) { 
		cookie_city($city);
		$currefer = udecode($currefer);
		if ($currefer) {
			redirect($currefer);
		} else if ( $_SERVER['HTTP_REFERER'] ) {
			if (!preg_match('#'.$_SERVER['HTTP_HOST'].'#', $_SERVER['HTTP_REFERER'])) {
				redirect( 'index.php');
			}
			if (preg_match('#/city#', $_SERVER['HTTP_REFERER'])) {
				redirect( 'index.php');
			}
			redirect($_SERVER['HTTP_REFERER']);
		}
		redirect('index.php');
	}
}

$cities = DB::LimitQuery('category', array(
	'condition' => array( 'zone' => 'city') ,
));
$cities = Utility::AssColumn($cities, 'letter', 'ename');
ksort($cities);
include template('wap_city');
