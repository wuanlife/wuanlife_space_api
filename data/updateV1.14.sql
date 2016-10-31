-- 2016/10/17  17:29
-- 新增数据表
-- user:小超
CREATE TABLE `wuan`.`message_text` (
	`message_detail_id` INT (11) NOT NULL COMMENT '消息ID',
	`text` VARCHAR (65) CHARACTER
SET utf8 COLLATE utf8_bin NOT NULL COMMENT '申请信息'
) ENGINE = INNODB CHARACTER
SET utf8 COLLATE utf8_bin COMMENT = '消息内容表';