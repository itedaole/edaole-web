<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/app.php');
need_manager();

$sql = array();

/*2014-10-07 start*/
$sql[] = "CREATE TABLE IF NOT EXISTS `comment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `partner_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `add_time` int(11) NOT NULL,
  `comment_content` text COMMENT '评论内容',
  `image1` varchar(255) DEFAULT NULL,
  `image2` varchar(255) DEFAULT NULL,
  `image3` varchar(255) DEFAULT NULL,
  `image4` varchar(255) DEFAULT NULL,
  `reply_cotent` text COMMENT '回复内容',
  `comment_grade` int(1) NOT NULL DEFAULT '5' COMMENT '总体评价',
  `comment_grade_all` text COMMENT '五星等级',
  `comment_grade_three` tinyint(1) NOT NULL DEFAULT '3' COMMENT '好中差评',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='用户评论表' AUTO_INCREMENT=7";

$sql[] = "CREATE TABLE IF NOT EXISTS `comment_partner` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `cat_id` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COMMENT='店铺动态评论表' AUTO_INCREMENT=5";
/*2014-10-07 end*/
$sql[] = " ALTER TABLE `order` CHANGE `trade_no` `trade_no` VARCHAR( 128 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL";
$sql[] = "ALTER TABLE `order` ADD `is_bill` INT NOT NULL DEFAULT '0' COMMENT '是否已结算'";
$sql[] = "CREATE TABLE IF NOT EXISTS `partner_bill` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `partner_id` int(8) NOT NULL,
  `bill_sn` int(11) NOT NULL,
  `add_time` int(11) NOT NULL,
  `end_time` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `bill_status` enum('0','1','2') NOT NULL,
  `bank_sn` varchar(60) DEFAULT NULL,
  `bank_name` varchar(60) DEFAULT NULL,
  `name` varchar(60) DEFAULT NULL,
  `mnote` text COMMENT '管理员备注',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4";
$sql[] = "ALTER TABLE `team` ADD `js_price` DOUBLE( 10, 2 ) NOT NULL DEFAULT '0.00' COMMENT '商家结算价格' AFTER `market_price`";
$sql[] = "ALTER TABLE `team` ADD `coupon_number` INT( 11 ) NOT NULL DEFAULT '0' COMMENT '消费数量' AFTER `pre_number`";
$sql[] = "ALTER TABLE `team` ADD `wap_detail` TEXT NULL DEFAULT NULL AFTER `detail`";

if(!empty($sql)){
  for($i = 0;$i < count($sql);$i++){
      $query = DB::Query($sql[$i]); 
  }

}
Session::Set('notice', '数据库结构升级成功，数据库已经是最新版本 OK!');
redirect( WEB_ROOT . '/manage/misc/index.php' );