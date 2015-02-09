<?php
require_once(dirname(dirname(__FILE__)) . '/app.php');
if ( $login_user_id ) {
echo "{'singer':{'userid':'".$login_user_id."','stat':'loginok'}} ";
}

