-- 2016/11/15   17:29
-- 新增用户收藏表
-- user:小超
CREATE TABLE
IF NOT EXISTS `user_collection` (
	`id` INT (11) NOT NULL AUTO_INCREMENT COMMENT '收藏id',
	`post_base_id` INT (11) NOT NULL COMMENT '帖子id',
    `user_base_id` INT (11) NOT NULL COMMENT '帖子id',
	`createTime` INT (10) NOT NULL COMMENT '收藏时间',
	`delete` TINYINT (1) NOT NULL DEFAULT '0'  COMMENT '是否删除',
	PRIMARY KEY (`id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_bin COMMENT = '用户收藏表' AUTO_INCREMENT = 1;
-- 2016/11/15   17:29
-- 修改用户细节表上次登录字段
-- user:小超
ALTER TABLE `user_detail` CHANGE `lastLogTime` `lastLogTime` INT (10) NOT NULL COMMENT '上次登录';
-- 2016/11/15   17:29
-- 修改用户细节表新增用户头像字段
-- user:小超
ALTER TABLE `user_detail` ADD `profile_picture` VARCHAR (255) CHARACTER
SET utf8 COLLATE utf8_bin NOT NULL COMMENT '用户头像';