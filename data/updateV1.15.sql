-- 2016/11/15   17:29
-- 新增用户收藏表
-- user:小超
CREATE TABLE
IF NOT EXISTS `user_collection` (
	`id` INT (11) NOT NULL AUTO_INCREMENT COMMENT '收藏id',
	`post_base_id` INT (11) NOT NULL COMMENT '帖子id',
    `user_base_id` INT (11) NOT NULL COMMENT '用户id',
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
SET utf8 COLLATE utf8_bin NULL COMMENT '用户头像';

INSERT INTO `wuan`.`message_base` (`code`, `type`, `content`)
VALUES
	('0004','3','{0}已将你从{1}中移除'),
	('0005','3','{0}已从你的{1}中退出'),
	('0006','3','{0}已加入你的{1}'),
	('0007','3','{0}回复我的主题{1}';