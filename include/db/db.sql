-- phpMyAdmin SQL Dump
-- version 西部数码修改多用户版
-- http://www.phpmyadmin.net
--
-- 主机: 
-- 生成日期: 2011 年 11 月 02 日 01:43
-- 服务器版本: 5.1.51
-- PHP 版本: 5.2.14

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES gb2312 */;

--
-- 数据库: `zuitu_db`
--

-- --------------------------------------------------------

--
-- 表的结构 `ask`
--

DROP TABLE IF EXISTS `ask`;
CREATE TABLE IF NOT EXISTS `ask` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('ask','transfer') NOT NULL DEFAULT 'ask',
  `content` text,
  `comment` text,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `card`
--

DROP TABLE IF EXISTS `card`;
CREATE TABLE IF NOT EXISTS `card` (
  `id` varchar(16) NOT NULL,
  `code` varchar(16) DEFAULT NULL,
  `partner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `credit` int(10) unsigned NOT NULL DEFAULT '0',
  `consume` enum('Y','N') NOT NULL DEFAULT 'N',
  `ip` varchar(16) DEFAULT NULL,
  `begin_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `cart`
--

DROP TABLE IF EXISTS `cart`;
CREATE TABLE IF NOT EXISTS `cart` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `detail` text,
  `money` double(10,2) NOT NULL DEFAULT '0.00',
  `action` varchar(16) NOT NULL DEFAULT 'buy',
  `state` varchar(16) NOT NULL DEFAULT 'unpay',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `zone` varchar(16) DEFAULT NULL,
  `czone` varchar(32) DEFAULT NULL,
  `name` varchar(32) DEFAULT NULL,
  `ename` varchar(16) DEFAULT NULL,
  `letter` char(1) DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `display` enum('Y','N') NOT NULL DEFAULT 'Y',
  `relate_data` text,
  `fid` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_zne` (`zone`,`name`,`ename`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `coupon`
--

DROP TABLE IF EXISTS `coupon`;
CREATE TABLE IF NOT EXISTS `coupon` (
  `id` varchar(16) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `partner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` enum('consume','credit') NOT NULL DEFAULT 'consume',
  `credit` int(10) unsigned NOT NULL DEFAULT '0',
  `secret` varchar(10) DEFAULT NULL,
  `consume` enum('Y','N') NOT NULL DEFAULT 'N',
  `ip` varchar(16) DEFAULT NULL,
  `sms` int(10) unsigned NOT NULL DEFAULT '0',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0',
  `consume_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `sms_time` int(10) unsigned NOT NULL DEFAULT '0',
  `buy_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `credit`
--

DROP TABLE IF EXISTS `credit`;
CREATE TABLE IF NOT EXISTS `credit` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `detail_id` varchar(32) DEFAULT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `score` double(10,2) NOT NULL DEFAULT '0.00',
  `action` varchar(16) NOT NULL DEFAULT 'buy',
  `rname` varchar(32) DEFAULT NULL,
  `rmobile` varchar(32) DEFAULT NULL,
  `rcode` char(6) DEFAULT NULL,
  `raddress` varchar(128) DEFAULT NULL,
  `send_time` int(10) DEFAULT NULL,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `state` enum('unpay','pay') NOT NULL DEFAULT 'unpay',
  `remark` text,
  `op_time` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `daysign`
--

DROP TABLE IF EXISTS `daysign`;
CREATE TABLE IF NOT EXISTS `daysign` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `credit` double(10,2) DEFAULT '0.00',
  `money` double(10,2) DEFAULT '0.00',
  `create_time` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `category` enum('suggest','seller') NOT NULL DEFAULT 'suggest',
  `title` varchar(128) DEFAULT NULL,
  `contact` varchar(255) DEFAULT NULL,
  `content` text,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `flow`
--

DROP TABLE IF EXISTS `flow`;
CREATE TABLE IF NOT EXISTS `flow` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `detail_id` varchar(32) DEFAULT NULL,
  `detail` varchar(255) DEFAULT NULL,
  `direction` enum('income','expense') NOT NULL DEFAULT 'income',
  `money` double(10,2) NOT NULL DEFAULT '0.00',
  `action` varchar(16) NOT NULL DEFAULT 'buy',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `friendlink`
--

DROP TABLE IF EXISTS `friendlink`;
CREATE TABLE IF NOT EXISTS `friendlink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(32) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `display` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_l` (`url`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `goods`
--

DROP TABLE IF EXISTS `goods`;
CREATE TABLE IF NOT EXISTS `goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `image` varchar(128) DEFAULT NULL,
  `time` int(11) NOT NULL DEFAULT '0',
  `number` int(11) NOT NULL DEFAULT '0',
  `per_number` int(11) NOT NULL DEFAULT '1',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `consume` int(11) NOT NULL DEFAULT '0',
  `display` enum('Y','N') NOT NULL DEFAULT 'Y',
  `enable` enum('Y','N') NOT NULL DEFAULT 'Y',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `invite`
--

DROP TABLE IF EXISTS `invite`;
CREATE TABLE IF NOT EXISTS `invite` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_ip` varchar(16) DEFAULT NULL,
  `other_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `other_user_ip` varchar(16) DEFAULT NULL,
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pay` enum('Y','N','C') NOT NULL DEFAULT 'N',
  `credit` int(10) unsigned NOT NULL DEFAULT '0',
  `buy_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_uo` (`user_id`,`other_user_id`),
  UNIQUE KEY `UNQ_o` (`other_user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `logger_admin`
--

DROP TABLE IF EXISTS `logger_admin`;
CREATE TABLE IF NOT EXISTS `logger_admin` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `user_email` varchar(128) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `operation` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `relate_data` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `create_on` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `mailer`
--

DROP TABLE IF EXISTS `mailer`;
CREATE TABLE IF NOT EXISTS `mailer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) DEFAULT NULL,
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `secret` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_e` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `news`
--

DROP TABLE IF EXISTS `news`;
CREATE TABLE IF NOT EXISTS `news` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  `detail` text,
  `begin_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `order`
--

DROP TABLE IF EXISTS `order`;
CREATE TABLE IF NOT EXISTS `order` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `pay_id` varchar(32) DEFAULT NULL,
  `buy_id` int(11) NOT NULL DEFAULT '0',
  `service` varchar(16) NOT NULL DEFAULT 'alipay',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `card_id` varchar(16) DEFAULT NULL,
  `state` enum('unpay','pay') NOT NULL DEFAULT 'unpay',
  `allowrefund` enum('Y','N') NOT NULL DEFAULT 'N',
  `rstate` enum('normal','askrefund','berefund','norefund') NOT NULL DEFAULT 'normal',
  `rereason` text,
  `retime` int(11) DEFAULT NULL,
  `quantity` int(10) unsigned NOT NULL DEFAULT '1',
  `realname` varchar(32) DEFAULT NULL,
  `mobile` varchar(128) DEFAULT NULL,
  `zipcode` char(6) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `express` enum('Y','N') NOT NULL DEFAULT 'Y',
  `express_xx` varchar(128) DEFAULT NULL,
  `express_id` int(10) unsigned NOT NULL DEFAULT '0',
  `express_no` varchar(32) DEFAULT NULL,
  `price` double(10,2) NOT NULL DEFAULT '0.00',
  `money` double(10,2) NOT NULL DEFAULT '0.00',
  `origin` double(10,2) NOT NULL DEFAULT '0.00',
  `credit` double(10,2) NOT NULL DEFAULT '0.00',
  `card` double(10,2) NOT NULL DEFAULT '0.00',
  `fare` double(10,2) NOT NULL DEFAULT '0.00',
  `condbuy` varchar(128) DEFAULT NULL,
  `remark` text,
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_content` text,
  `comment_display` enum('Y','N') NOT NULL DEFAULT 'Y',
  `comment_grade` enum('good','none','bad') NOT NULL DEFAULT 'good',
  `comment_wantmore` enum('Y','N') DEFAULT NULL,
  `comment_time` int(11) DEFAULT NULL,
  `partner_id` int(11) NOT NULL DEFAULT '0',
  `sms_express` enum('Y','N') NOT NULL DEFAULT 'N',
  `luky_id` int(11) NOT NULL DEFAULT '0',
  `adminremark` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_p` (`pay_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE IF NOT EXISTS `page` (
  `id` varchar(16) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `partner`
--

DROP TABLE IF EXISTS `partner`;
CREATE TABLE IF NOT EXISTS `partner` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `title` varchar(128) DEFAULT NULL,
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `homepage` varchar(128) DEFAULT NULL,
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bank_name` varchar(128) DEFAULT NULL,
  `bank_no` varchar(128) DEFAULT NULL,
  `bank_user` varchar(128) DEFAULT NULL,
  `location` text NOT NULL,
  `contact` varchar(32) DEFAULT NULL,
  `image` varchar(128) DEFAULT NULL,
  `image1` varchar(128) DEFAULT NULL,
  `image2` varchar(128) DEFAULT NULL,
  `phone` varchar(18) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `other` text,
  `mobile` varchar(12) DEFAULT NULL,
  `open` enum('Y','N') NOT NULL DEFAULT 'N',
  `enable` enum('Y','N') NOT NULL DEFAULT 'Y',
  `head` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `longlat` varchar(255) DEFAULT NULL,
  `display` enum('Y','N') NOT NULL DEFAULT 'Y',
  `comment_good` int(11) NOT NULL DEFAULT '0',
  `comment_none` int(11) NOT NULL DEFAULT '0',
  `comment_bad` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_ct` (`city_id`,`title`),
  UNIQUE KEY `UNQ_u` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `pay`
--

DROP TABLE IF EXISTS `pay`;
CREATE TABLE IF NOT EXISTS `pay` (
  `id` varchar(32) NOT NULL DEFAULT '',
  `vid` varchar(32) DEFAULT NULL,
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `bank` varchar(32) DEFAULT NULL,
  `money` double(10,2) DEFAULT NULL,
  `currency` enum('CNY','USD') NOT NULL DEFAULT 'CNY',
  `service` varchar(16) NOT NULL DEFAULT 'alipay',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_o` (`order_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `paycard`
--

DROP TABLE IF EXISTS `paycard`;
CREATE TABLE IF NOT EXISTS `paycard` (
  `id` varchar(16) NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `value` int(10) unsigned NOT NULL DEFAULT '0',
  `consume` enum('Y','N') NOT NULL DEFAULT 'N',
  `recharge_time` int(10) unsigned NOT NULL DEFAULT '0',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `referer`
--

DROP TABLE IF EXISTS `referer`;
CREATE TABLE IF NOT EXISTS `referer` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `order_id` int(11) DEFAULT NULL,
  `user_id` bigint(20) unsigned NOT NULL COMMENT '??id',
  `referer` varchar(400) COLLATE utf8_unicode_ci NOT NULL COMMENT '??',
  `create_time` int(10) unsigned NOT NULL COMMENT '????',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_o` (`order_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='??';

-- --------------------------------------------------------

--
-- 表的结构 `smssubscribe`
--

DROP TABLE IF EXISTS `smssubscribe`;
CREATE TABLE IF NOT EXISTS `smssubscribe` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `mobile` varchar(18) DEFAULT NULL,
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `secret` char(6) DEFAULT NULL,
  `enable` enum('Y','N') NOT NULL DEFAULT 'N',
  `create_time` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_e` (`mobile`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `subscribe`
--

DROP TABLE IF EXISTS `subscribe`;
CREATE TABLE IF NOT EXISTS `subscribe` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) DEFAULT NULL,
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `secret` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_e` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `system`
--

DROP TABLE IF EXISTS `system`;
CREATE TABLE IF NOT EXISTS `system` (
  `id` enum('1') NOT NULL DEFAULT '1',
  `value` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `team`
--

DROP TABLE IF EXISTS `team`;
CREATE TABLE IF NOT EXISTS `team` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(128) DEFAULT NULL,
  `summary` text,
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `city_ids` text,
  `group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `partner_id` int(10) unsigned NOT NULL DEFAULT '0',
  `system` enum('Y','N') NOT NULL DEFAULT 'Y',
  `team_price` double(10,2) NOT NULL DEFAULT '0.00',
  `market_price` double(10,2) NOT NULL DEFAULT '0.00',
  `product` varchar(128) DEFAULT NULL,
  `condbuy` varchar(255) DEFAULT NULL,
  `per_number` int(10) unsigned NOT NULL DEFAULT '1',
  `permin_number` int(10) DEFAULT '1',
  `min_number` int(10) unsigned NOT NULL DEFAULT '1',
  `max_number` int(10) unsigned NOT NULL DEFAULT '0',
  `now_number` int(10) unsigned NOT NULL DEFAULT '0',
  `pre_number` int(10) unsigned NOT NULL DEFAULT '0',
  `allowrefund` enum('Y','N') NOT NULL DEFAULT 'N',
  `image` varchar(128) DEFAULT NULL,
  `image1` varchar(128) DEFAULT NULL,
  `image2` varchar(128) DEFAULT NULL,
  `flv` varchar(128) DEFAULT NULL,
  `mobile` varchar(16) DEFAULT NULL,
  `credit` int(10) unsigned NOT NULL DEFAULT '0',
  `card` int(10) unsigned NOT NULL DEFAULT '0',
  `fare` int(10) unsigned NOT NULL DEFAULT '0',
  `farefree` int(11) NOT NULL DEFAULT '0',
  `bonus` int(11) NOT NULL DEFAULT '0',
  `address` varchar(128) DEFAULT NULL,
  `detail` text,
  `systemreview` text,
  `userreview` text,
  `notice` text,
  `express` text,
  `delivery` varchar(16) NOT NULL DEFAULT 'coupon',
  `state` enum('none','success','soldout','failure','refund') NOT NULL DEFAULT 'none',
  `conduser` enum('Y','N') NOT NULL DEFAULT 'Y',
  `buyonce` enum('Y','N') NOT NULL DEFAULT 'Y',
  `team_type` varchar(20) DEFAULT 'normal',
  `sort_order` int(11) NOT NULL DEFAULT '0',
  `expire_time` int(10) unsigned NOT NULL DEFAULT '0',
  `begin_time` int(10) unsigned NOT NULL DEFAULT '0',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0',
  `reach_time` int(10) unsigned NOT NULL DEFAULT '0',
  `close_time` int(10) unsigned NOT NULL DEFAULT '0',
  `seo_title` varchar(255) DEFAULT NULL,
  `seo_keyword` varchar(255) DEFAULT NULL,
  `seo_description` text,
  `express_relate` text,
  `sub_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `toolsbind`
--

DROP TABLE IF EXISTS `toolsbind`;
CREATE TABLE IF NOT EXISTS `toolsbind` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL,
  `tools` varchar(16) NOT NULL,
  `secret` varchar(16) DEFAULT NULL,
  `enable` enum('Y','N') NOT NULL,
  `create_time` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `topic`
--

DROP TABLE IF EXISTS `topic`;
CREATE TABLE IF NOT EXISTS `topic` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `title` varchar(128) DEFAULT NULL,
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `public_id` int(10) unsigned NOT NULL DEFAULT '0',
  `content` text,
  `head` int(10) unsigned NOT NULL DEFAULT '0',
  `reply_number` int(10) unsigned NOT NULL DEFAULT '0',
  `view_number` int(10) unsigned NOT NULL DEFAULT '0',
  `last_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `last_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(128) DEFAULT NULL,
  `username` varchar(32) DEFAULT NULL,
  `realname` varchar(32) DEFAULT NULL,
  `alipay_id` varchar(32) DEFAULT NULL,
  `password` varchar(32) DEFAULT NULL,
  `avatar` varchar(128) DEFAULT NULL,
  `gender` enum('M','F') NOT NULL DEFAULT 'M',
  `newbie` enum('Y','N') NOT NULL DEFAULT 'Y',
  `mobile` varchar(16) DEFAULT NULL,
  `qq` varchar(16) DEFAULT NULL,
  `money` double(10,2) NOT NULL DEFAULT '0.00',
  `score` int(11) NOT NULL DEFAULT '0',
  `zipcode` char(6) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city_id` int(10) unsigned NOT NULL DEFAULT '0',
  `emailable` enum('Y','N') NOT NULL DEFAULT 'Y',
  `enable` enum('Y','N') NOT NULL DEFAULT 'Y',
  `manager` enum('Y','N') NOT NULL DEFAULT 'N',
  `secret` varchar(32) DEFAULT NULL,
  `recode` varchar(32) DEFAULT NULL,
  `sns` varchar(64) DEFAULT NULL,
  `ip` varchar(16) DEFAULT NULL,
  `login_time` int(10) unsigned NOT NULL DEFAULT '0',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0',
  `mobilecode` char(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_name` (`username`),
  UNIQUE KEY `UNQ_e` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vote_feedback`
--

DROP TABLE IF EXISTS `vote_feedback`;
CREATE TABLE IF NOT EXISTS `vote_feedback` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `ip` varchar(15) NOT NULL DEFAULT '',
  `addtime` char(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vote_feedback_input`
--

DROP TABLE IF EXISTS `vote_feedback_input`;
CREATE TABLE IF NOT EXISTS `vote_feedback_input` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` bigint(20) unsigned NOT NULL,
  `options_id` mediumint(8) unsigned NOT NULL,
  `value` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vote_feedback_question`
--

DROP TABLE IF EXISTS `vote_feedback_question`;
CREATE TABLE IF NOT EXISTS `vote_feedback_question` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `feedback_id` bigint(20) unsigned NOT NULL,
  `question_id` mediumint(8) unsigned NOT NULL,
  `options_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vote_options`
--

DROP TABLE IF EXISTS `vote_options`;
CREATE TABLE IF NOT EXISTS `vote_options` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` mediumint(8) unsigned NOT NULL,
  `name` varchar(60) NOT NULL,
  `is_br` char(1) NOT NULL DEFAULT '0',
  `is_input` char(1) NOT NULL DEFAULT '0',
  `is_show` char(1) NOT NULL DEFAULT '1',
  `order` mediumint(8) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `vote_question`
--

DROP TABLE IF EXISTS `vote_question`;
CREATE TABLE IF NOT EXISTS `vote_question` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `type` varchar(10) NOT NULL DEFAULT 'radio',
  `is_show` char(1) NOT NULL DEFAULT '1',
  `addtime` char(10) NOT NULL,
  `order` mediumint(8) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `voucher`
--

DROP TABLE IF EXISTS `voucher`;
CREATE TABLE IF NOT EXISTS `voucher` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(64) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `team_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  `sms` int(10) unsigned NOT NULL DEFAULT '0',
  `sms_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNQ_ct` (`code`,`team_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
