-- 2016/09/08  13:07
-- 新增帖子的锁定字段
-- user:小超
ALTER TABLE `post_base`  ADD `lock` INT(1) NOT NULL DEFAULT '0' COMMENT '锁定帖子';
-- 2016/09/08  13:10
-- 新增星球的私密字段
-- user:小超
ALTER TABLE `group_base`  ADD `private` INT(1) NOT NULL DEFAULT '0' COMMENT '私密星球';
-- 2016/09/08  13:18
-- 新增消息基础表，包括消息码和消息内容
-- user:小超
CREATE TABLE
IF NOT EXISTS `message_base` (
	`code` VARCHAR (4) NOT NULL COMMENT '消息码',
	`content` VARCHAR (30) NOT NULL COMMENT '消息内容',
	PRIMARY KEY (`code`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_bin COMMENT = '消息模板表';

--
-- 转存表中的数据 `message_base`
--
INSERT INTO `message_base` (`code`, `content`)
VALUES
	(
		'0001',
		'{0}申请加入{1}星球。'
	),
	(
		'0002',
		'{0}同意你加入{1}星球。'
	),
	(
		'0003',
		'你申请加入{1}星球已被{0}拒绝。'
	);

-- 2016/09/08  13:18
-- 新增用户消息表，包括消息码、用户id、标识码、创建时间、是否已读
-- user:小超
CREATE TABLE
IF NOT EXISTS `message_detail` (
	`message_base_code` VARCHAR (4) NOT NULL COMMENT '消息码' REFERENCES `message_base`(`code`),
	`user_base_id` INT (5) NOT NULL COMMENT '用户id' REFERENCES `user_base`(`id`),
	`count` INT (5) NOT NULL COMMENT '前两个字段相同，则改为2',
	`id_1` INT (5) NOT NULL COMMENT '申请人或创建人id',
	`id_2` INT (5) NOT NULL COMMENT '星球id',
	`createTime` INT (10) NOT NULL COMMENT '创建时间',
	`saw` INT (1) NOT NULL DEFAULT '0' COMMENT '是否已读',
	PRIMARY KEY (
		`message_base_code`,
		`user_base_id`,
		`count`
	)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_bin COMMENT = '用户消息表';

