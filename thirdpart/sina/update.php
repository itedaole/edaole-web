<?php
require_once(dirname(__FILE__) . '/config.php');

DB::Query('alter table user add column sns varchar(32) after ip;');

var_dump('Update success');
