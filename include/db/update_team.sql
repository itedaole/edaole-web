/* 更新于 ZuituGo_Patch_CV2.0_23369_23934.tar.gz */
ALTER TABLE `team` ADD `express_relate` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '快递数据,序列化' AFTER `express`;
ALTER TABLE `team` ADD `city_ids` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci COMMENT '选择发布的城市列表' AFTER `city_id` ;
