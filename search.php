<?php
require_once(dirname(__FILE__) . '/app.php');


if($_POST){

   $m = $_POST['m'];
   $keywords = $_POST['keywords'];
   
   if($m == 'Goods'){
      redirect( WEB_ROOT . '/category.php?keywords='.$keywords);
   }else if($m == 'Supplier'){
      redirect( WEB_ROOT . '/partner/index.php?keywords='.$keywords);
   }

}

