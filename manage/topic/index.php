<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');

need_manager();

$condition = array();

/* filter */
$title = strval($_GET['title']);
if ($title ) {
	$condition[] = "title LIKE '%".mysql_escape_string($title)."%'";
}

/* filter end */

$count = Table::Count('xh_topic', $condition);
list($pagesize, $offset, $pagestring) = pagestring($count, 20);

$topic_list = DB::LimitQuery('xh_topic', array(
	'condition' => $condition,
	'order' => 'ORDER BY id DESC',
	'size' => $pagesize,
	'offset' => $offset,
));

include template('manage_topic_index');
