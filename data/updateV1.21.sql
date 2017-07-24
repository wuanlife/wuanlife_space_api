# 添加外键 7:45 2017/7/24
ALTER TABLE `group_detail` ADD FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`);
ALTER TABLE `group_detail` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `message_apply`
MODIFY COLUMN `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '消息id' FIRST ,
MODIFY COLUMN `user_base_id`  int(11) UNSIGNED NOT NULL COMMENT '接收者id' AFTER `text`,
MODIFY COLUMN `group_base_id`  int(11) UNSIGNED NOT NULL COMMENT '星球id' AFTER `user_base_id`,
MODIFY COLUMN `user_apply_id`  int(11) UNSIGNED NOT NULL COMMENT '申请者id' AFTER `group_base_id`;
ALTER TABLE `message_apply` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `message_apply` ADD FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`);
ALTER TABLE `message_apply` ADD FOREIGN KEY (`user_apply_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `message_notice`
MODIFY COLUMN `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '消息id' FIRST ,
MODIFY COLUMN `user_base_id`  int(11) UNSIGNED NOT NULL COMMENT '接收者id' AFTER `id`,
MODIFY COLUMN `group_base_id`  int(11) UNSIGNED NOT NULL COMMENT '星球id' AFTER `user_base_id`,
MODIFY COLUMN `user_notice_id`  int(11) UNSIGNED NOT NULL COMMENT '通知人id' AFTER `group_base_id`;
ALTER TABLE `message_notice` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `message_notice` ADD FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`);
ALTER TABLE `message_notice` ADD FOREIGN KEY (`user_notice_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `message_reply`
MODIFY COLUMN `id`  int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '消息id' FIRST ,
MODIFY COLUMN `user_base_id`  int(11) UNSIGNED NOT NULL COMMENT '接收者id' AFTER `id`,
MODIFY COLUMN `user_reply_id`  int(11) UNSIGNED NOT NULL COMMENT '回复者id' AFTER `user_base_id`,
MODIFY COLUMN `post_base_id`  int(11) UNSIGNED NOT NULL COMMENT '帖子id' AFTER `user_reply_id`;
ALTER TABLE `message_reply` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `message_reply` ADD FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`);
ALTER TABLE `message_reply` ADD FOREIGN KEY (`user_reply_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `post_approved`
MODIFY COLUMN `user_base_id`  int(11) UNSIGNED NOT NULL FIRST ,
MODIFY COLUMN `post_base_id`  int(11) UNSIGNED NOT NULL AFTER `user_base_id`,
MODIFY COLUMN `floor`  int(11) UNSIGNED NOT NULL AFTER `post_base_id`;
ALTER TABLE `post_approved` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `post_approved` ADD FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`);
ALTER TABLE `post_base` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `post_base` ADD FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`);
ALTER TABLE `post_detail`
MODIFY COLUMN `reply_id`  int(5) UNSIGNED NULL DEFAULT NULL AFTER `user_base_id`;
ALTER TABLE `post_detail` ADD FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`);
ALTER TABLE `post_detail` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `post_detail` ADD FOREIGN KEY (`reply_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `user_code`
MODIFY COLUMN `user_base_id`  int(11) UNSIGNED NOT NULL AFTER `used`;
ALTER TABLE `user_code` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);
ALTER TABLE `user_collection`
MODIFY COLUMN `post_base_id`  int(11) UNSIGNED NOT NULL COMMENT '帖子id' FIRST ,
MODIFY COLUMN `user_base_id`  int(11) UNSIGNED NOT NULL COMMENT '用户id' AFTER `post_base_id`;
ALTER TABLE `user_collection` ADD FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`);
ALTER TABLE `user_collection` ADD FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`);

# 创建帖子内容表 7:46 2017/7/24
DROP TABLE IF EXISTS `post_content`;
CREATE TABLE `post_content` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `content` text COLLATE utf8_bin COMMENT '内容',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `modify_time` int(10) unsigned DEFAULT NULL COMMENT '上次修改时间',
  PRIMARY KEY (`post_base_id`),
  CONSTRAINT `post_content_ibfk_1` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='帖子内容表';

# 将原有数据导入帖子内容表 7:47 2017/7/24
INSERT INTO post_content (
	`post_base_id`,
	`content`,
	`create_time`,
	`modify_time`
) SELECT
	post_detail.post_base_id,
	post_detail.text,
	UNIX_TIMESTAMP(post_detail.create_time),
	UNIX_TIMESTAMP(post_detail.create_time)
FROM
	post_detail
WHERE
	post_detail.floor = 1;
	
# 创建帖子回复表 9:48 2017/7/24
DROP TABLE IF EXISTS `post_comment`;
CREATE TABLE `post_comment` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '回帖人id',
  `comment` varchar(5000) COLLATE utf8_bin NOT NULL COMMENT '评论内容',
  `floor` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '楼层',
  `create_time` int(10) unsigned NOT NULL COMMENT '评论时间',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `reply_floor` int(5) unsigned NOT NULL DEFAULT '1' COMMENT '评论楼层',
  PRIMARY KEY (`post_base_id`,`floor`),
  KEY `user_base_id` (`user_base_id`),
  KEY `post_base_id` (`post_base_id`),
  KEY `floor` (`floor`),
  KEY `reply_floor` (`reply_floor`),
  CONSTRAINT `post_comment_ibfk_1` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`),
  CONSTRAINT `post_comment_ibfk_2` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='帖子评论表';

# 将原有数据导入帖子回复表 9:50 2017/7/24
INSERT INTO `post_comment` (
	`post_base_id`,
	`user_base_id`,
	`comment`,
	`floor`,
	`create_time`,
	`delete`,
	`reply_floor`
) SELECT
	post_detail.post_base_id,
	post_detail.user_base_id,
	post_detail.text,
	post_detail.floor,
	UNIX_TIMESTAMP(post_detail.create_time),
	post_detail.`delete`,
	post_detail.reply_floor
FROM
	post_detail
WHERE
	post_detail.floor > 1;

# 更新帖子回复表中的错误数据
UPDATE post_comment
SET post_comment.reply_floor = 1
WHERE
	post_comment.reply_floor = 0;