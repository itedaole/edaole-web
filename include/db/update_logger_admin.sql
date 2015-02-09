CREATE TABLE IF NOT EXISTS `logger_admin` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL COMMENT '用户id',
  `user_email` varchar(128) COLLATE utf8_unicode_ci NOT NULL COMMENT '用户邮箱',
  `type` varchar(10) COLLATE utf8_unicode_ci NOT NULL COMMENT '标识类型',
  `operation` text COLLATE utf8_unicode_ci NOT NULL COMMENT '操作信息',
  `relate_data` text COLLATE utf8_unicode_ci NOT NULL COMMENT '关联数据',
  `create_on` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='管理员操作日志' AUTO_INCREMENT=2 ;
