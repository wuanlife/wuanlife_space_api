-- ----------------------------
-- Table structure for message_apply
-- ----------------------------
DROP TABLE IF EXISTS `message_apply`;
CREATE TABLE `message_apply` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `text` varchar(100) COLLATE utf8_bin NOT NULL COMMENT '申请理由',
  `user_base_id` int(11) NOT NULL COMMENT '接收者id',
  `group_base_id` int(11) NOT NULL COMMENT '星球id',
  `user_apply_id` int(11) NOT NULL COMMENT '申请者id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL COMMENT '消息状态码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息私密申请表';

-- ----------------------------
-- Table structure for message_notice
-- ----------------------------
DROP TABLE IF EXISTS `message_notice`;
CREATE TABLE `message_notice` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `user_base_id` int(11) NOT NULL COMMENT '接收者id',
  `group_base_id` int(11) NOT NULL COMMENT '星球id',
  `user_notice_id` int(11) NOT NULL COMMENT '通知人id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL COMMENT '消息状态码',
  `type` int(11) NOT NULL COMMENT '消息类型',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息通知表';

-- ----------------------------
-- Table structure for message_reply
-- ----------------------------
DROP TABLE IF EXISTS `message_reply`;
CREATE TABLE `message_reply` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `user_base_id` int(11) NOT NULL COMMENT '接收者id',
  `user_reply_id` int(11) NOT NULL COMMENT '回复者id',
  `post_base_id` int(11) NOT NULL COMMENT '帖子id',
  `reply_floor` int(11) NOT NULL COMMENT '回复楼层',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL COMMENT '消息状态码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息帖子回复表';

-- ----------------------------
-- 更新消息私密星球申请表
-- ----------------------------
INSERT INTO message_apply (
	id,
	user_base_id,
	group_base_id,
	user_apply_id,
	create_time,
	`status`,
	text
) SELECT
	t2.message_id,
	t2.user_base_id,
	t2.id_2,
	t2.id_1,
	t2.createTime,
	t2. STATUS,
	t3.text
FROM
	message_detail t2,
	message_text t3
WHERE
	t2.message_id = t3.message_detail_id
AND t2.message_base_code = '0001';

-- ----------------------------
-- 更新帖子回复表
-- ----------------------------
INSERT INTO message_reply (
	id,
	user_base_id,
	post_base_id,
	user_reply_id,
	create_time,
	`status`,
	reply_floor
) SELECT
	t2.message_id,
	t2.user_base_id,
	t2.id_2,
	t2.id_1,
	t2.createTime,
	t2. STATUS,
	t3.text
FROM
	message_detail t2,
	message_text t3
WHERE
	t2.message_id = t3.message_detail_id
AND t2.message_base_code = '0007';

-- ----------------------------
-- 更新消息通知表
-- ----------------------------
INSERT INTO message_notice (
	id,
	user_base_id,
	group_base_id,
	user_notice_id,
	create_time,
	`status`,
	type
)SELECT
	t2.message_id,
	t2.user_base_id,
	t2.id_2,
	t2.id_1,
	t2.createTime,
	t2.status,
	t2.message_base_code-1
FROM
	message_detail t2
WHERE
	t2.message_base_code!='0007'
AND
	t2.message_base_code!='0001';

-- ----------------------------
-- 更新其他表字段
-- ----------------------------
ALTER TABLE post_detail CHANGE createTime create_time varchar(20);
ALTER TABLE post_detail CHANGE replyid reply_id INT(5);
ALTER TABLE post_detail CHANGE replyFloor reply_floor INT(5);
ALTER TABLE user_base CHANGE Email email varchar(30);
ALTER TABLE user_code CHANGE getpasstime get_pass_time int(10);
ALTER TABLE user_code CHANGE id uesr_base_id int(11);
ALTER TABLE user_collection CHANGE createTime create_time INT(10);
ALTER TABLE user_detail CHANGE lastLogTime last_logtime int(10);
ALTER TABLE user_detail CHANGE mailChecked mail_checked varchar(2);
ALTER TABLE post_approved CHANGE user_id uesr_base_id int(11);
ALTER TABLE post_approved CHANGE post_id post_base_id int(11);