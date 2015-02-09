<?php
require_once(dirname(__FILE__) . '/app.php');

$id = abs(intval($_GET['id']));
if (!$id || !$news = Table::FetchForce('news', $id) ) {
	redirect( WEB_ROOT . '/index.php');
}

include template('news_view');
