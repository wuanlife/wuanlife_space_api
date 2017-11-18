/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : wuan120

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2017-11-18 18:07:37
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for authorization
-- ----------------------------
DROP TABLE IF EXISTS `authorization`;
CREATE TABLE `authorization` (
  `area_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限位置区分',
  `aser_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限区分',
  `note` varchar(8) COLLATE utf8_bin NOT NULL COMMENT '说明'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='权限表';

-- ----------------------------
-- Table structure for group_base
-- ----------------------------
DROP TABLE IF EXISTS `group_base`;
CREATE TABLE `group_base` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '组id',
  `name` varchar(21) COLLATE utf8_bin NOT NULL COMMENT '组名',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `g_image` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '组图片',
  `g_introduction` varchar(50) COLLATE utf8_bin DEFAULT NULL COMMENT '组介绍',
  `private` int(1) NOT NULL DEFAULT '0' COMMENT '私密星球',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组表';

-- ----------------------------
-- Table structure for group_detail
-- ----------------------------
DROP TABLE IF EXISTS `group_detail`;
CREATE TABLE `group_detail` (
  `group_base_id` int(4) unsigned NOT NULL COMMENT '组id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '成员id',
  `authorization` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限',
  PRIMARY KEY (`group_base_id`,`user_base_id`),
  KEY `user_base_id` (`user_base_id`),
  CONSTRAINT `group_detail_ibfk_1` FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`),
  CONSTRAINT `group_detail_ibfk_2` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组成员表';

-- ----------------------------
-- Table structure for message_apply
-- ----------------------------
DROP TABLE IF EXISTS `message_apply`;
CREATE TABLE `message_apply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `text` varchar(80) COLLATE utf8_bin DEFAULT NULL COMMENT '申请理由',
  `user_base_id` int(11) unsigned NOT NULL COMMENT '接收者id',
  `group_base_id` int(11) unsigned NOT NULL COMMENT '星球id',
  `user_apply_id` int(11) unsigned NOT NULL COMMENT '申请者id',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL COMMENT '消息状态码',
  PRIMARY KEY (`id`),
  KEY `user_base_id` (`user_base_id`),
  KEY `group_base_id` (`group_base_id`),
  KEY `user_apply_id` (`user_apply_id`),
  CONSTRAINT `message_apply_ibfk_1` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`),
  CONSTRAINT `message_apply_ibfk_2` FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`),
  CONSTRAINT `message_apply_ibfk_3` FOREIGN KEY (`user_apply_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息私密申请表';

-- ----------------------------
-- Table structure for message_base
-- ----------------------------
DROP TABLE IF EXISTS `message_base`;
CREATE TABLE `message_base` (
  `code` varchar(4) COLLATE utf8_bin NOT NULL COMMENT '消息码',
  `type` int(2) NOT NULL COMMENT '消息类型',
  `content` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '消息内容',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息模板表';

-- ----------------------------
-- Table structure for message_detail
-- ----------------------------
DROP TABLE IF EXISTS `message_detail`;
CREATE TABLE `message_detail` (
  `message_id` int(5) NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `message_base_code` varchar(4) COLLATE utf8_bin NOT NULL COMMENT '消息码',
  `user_base_id` int(5) NOT NULL COMMENT '用户id',
  `id_1` int(5) NOT NULL COMMENT '申请人或创建人id',
  `id_2` int(5) NOT NULL COMMENT '星球id',
  `createTime` int(10) NOT NULL COMMENT '创建时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '消息的状态',
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户消息表';

-- ----------------------------
-- Table structure for message_notice
-- ----------------------------
DROP TABLE IF EXISTS `message_notice`;
CREATE TABLE `message_notice` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `user_base_id` int(11) unsigned NOT NULL COMMENT '接收者id',
  `group_base_id` int(11) unsigned NOT NULL COMMENT '星球id',
  `user_notice_id` int(11) unsigned NOT NULL COMMENT '通知人id',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL COMMENT '消息状态码',
  `type` int(11) NOT NULL COMMENT '消息类型',
  PRIMARY KEY (`id`),
  KEY `user_base_id` (`user_base_id`),
  KEY `group_base_id` (`group_base_id`),
  KEY `user_notice_id` (`user_notice_id`),
  CONSTRAINT `message_notice_ibfk_1` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`),
  CONSTRAINT `message_notice_ibfk_2` FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`),
  CONSTRAINT `message_notice_ibfk_3` FOREIGN KEY (`user_notice_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息通知表';

-- ----------------------------
-- Table structure for message_reply
-- ----------------------------
DROP TABLE IF EXISTS `message_reply`;
CREATE TABLE `message_reply` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '消息id',
  `user_base_id` int(11) unsigned NOT NULL COMMENT '接收者id',
  `user_reply_id` int(11) unsigned NOT NULL COMMENT '回复者id',
  `post_base_id` int(11) unsigned NOT NULL COMMENT '帖子id',
  `reply_floor` int(11) NOT NULL COMMENT '回复楼层',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `status` int(11) NOT NULL COMMENT '消息状态码',
  PRIMARY KEY (`id`),
  KEY `user_base_id` (`user_base_id`),
  KEY `post_base_id` (`post_base_id`),
  KEY `user_reply_id` (`user_reply_id`),
  CONSTRAINT `message_reply_ibfk_1` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`),
  CONSTRAINT `message_reply_ibfk_2` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`),
  CONSTRAINT `message_reply_ibfk_3` FOREIGN KEY (`user_reply_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息帖子回复表';

-- ----------------------------
-- Table structure for message_text
-- ----------------------------
DROP TABLE IF EXISTS `message_text`;
CREATE TABLE `message_text` (
  `message_detail_id` int(11) NOT NULL COMMENT '消息ID',
  `text` varchar(65) COLLATE utf8_bin NOT NULL COMMENT '申请信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息内容表';

-- ----------------------------
-- Table structure for post_approved
-- ----------------------------
DROP TABLE IF EXISTS `post_approved`;
CREATE TABLE `post_approved` (
  `user_base_id` int(11) unsigned NOT NULL,
  `post_base_id` int(11) unsigned NOT NULL,
  `floor` int(11) unsigned NOT NULL,
  `approved` tinyint(1) NOT NULL,
  PRIMARY KEY (`user_base_id`,`post_base_id`,`floor`),
  KEY `post_base_id` (`post_base_id`),
  CONSTRAINT `post_approved_ibfk_1` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`),
  CONSTRAINT `post_approved_ibfk_2` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='帖子点赞表';

-- ----------------------------
-- Table structure for post_base
-- ----------------------------
DROP TABLE IF EXISTS `post_base`;
CREATE TABLE `post_base` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '发帖人id',
  `group_base_id` int(4) unsigned NOT NULL COMMENT '组id',
  `title` varchar(61) COLLATE utf8_bin NOT NULL COMMENT '标题',
  `digest` int(1) NOT NULL DEFAULT '0' COMMENT '精华',
  `sticky` int(1) NOT NULL DEFAULT '0' COMMENT '置顶',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `lock` int(1) NOT NULL DEFAULT '0' COMMENT '锁定帖子',
  PRIMARY KEY (`id`),
  KEY `user_base_id` (`user_base_id`,`group_base_id`),
  KEY `group_base_id` (`group_base_id`),
  CONSTRAINT `post_base_ibfk_1` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`),
  CONSTRAINT `post_base_ibfk_2` FOREIGN KEY (`group_base_id`) REFERENCES `group_base` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='主帖';

-- ----------------------------
-- Table structure for post_comment
-- ----------------------------
DROP TABLE IF EXISTS `post_comment`;
CREATE TABLE `post_comment` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '回复人id',
  `comment` varchar(5000) COLLATE utf8_bin NOT NULL COMMENT '评论内容',
  `floor` int(4) unsigned NOT NULL COMMENT '楼层',
  `create_time` int(10) unsigned NOT NULL COMMENT '评论时间',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  `reply_id` int(5) unsigned DEFAULT NULL COMMENT '被评论的用户ID',
  `reply_floor` int(5) unsigned NOT NULL DEFAULT '1' COMMENT '评论楼层',
  PRIMARY KEY (`post_base_id`,`floor`),
  KEY `user_base_id` (`user_base_id`),
  KEY `post_base_id` (`post_base_id`),
  KEY `floor` (`floor`),
  KEY `reply_floor` (`reply_floor`),
  CONSTRAINT `post_comment_ibfk_1` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`),
  CONSTRAINT `post_comment_ibfk_2` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='帖子评论表';

-- ----------------------------
-- Table structure for post_content
-- ----------------------------
DROP TABLE IF EXISTS `post_content`;
CREATE TABLE `post_content` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `content` text COLLATE utf8_bin COMMENT '内容',
  `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
  `modify_time` int(10) unsigned DEFAULT NULL COMMENT '上次修改时间',
  PRIMARY KEY (`post_base_id`),
  CONSTRAINT `post_content_ibfk_1` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='帖子内容表';

-- ----------------------------
-- Table structure for post_detail
-- ----------------------------
DROP TABLE IF EXISTS `post_detail`;
CREATE TABLE `post_detail` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '回帖人id',
  `reply_id` int(5) unsigned DEFAULT NULL,
  `text` varchar(5000) COLLATE utf8_bin NOT NULL COMMENT '内容',
  `floor` int(4) NOT NULL COMMENT '楼层',
  `create_time` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `reply_floor` int(5) DEFAULT NULL,
  PRIMARY KEY (`post_base_id`,`floor`),
  KEY `user_base_id` (`user_base_id`,`reply_id`),
  KEY `reply_id` (`reply_id`),
  CONSTRAINT `post_detail_ibfk_1` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`),
  CONSTRAINT `post_detail_ibfk_2` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`),
  CONSTRAINT `post_detail_ibfk_3` FOREIGN KEY (`reply_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='回复帖';

-- ----------------------------
-- Table structure for post_image
-- ----------------------------
DROP TABLE IF EXISTS `post_image`;
CREATE TABLE `post_image` (
  `post_base_id` int(11) NOT NULL COMMENT '帖子id',
  `post_image_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '图片id',
  `p_image` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '帖子图片',
  `delete` int(11) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`post_image_id`,`post_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='?ظ?????ͼƬ';

-- ----------------------------
-- Table structure for user_base
-- ----------------------------
DROP TABLE IF EXISTS `user_base`;
CREATE TABLE `user_base` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `password` varchar(35) COLLATE utf8_bin NOT NULL COMMENT '密码',
  `nickname` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '昵称',
  `email` varchar(30) COLLATE utf8_bin DEFAULT NULL,
  `regtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `Email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=178 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表基本';

-- ----------------------------
-- Table structure for user_code
-- ----------------------------
DROP TABLE IF EXISTS `user_code`;
CREATE TABLE `user_code` (
  `get_pass_time` int(10) DEFAULT NULL,
  `code` varchar(11) COLLATE utf8_bin NOT NULL,
  `difference` int(11) NOT NULL COMMENT '区别',
  `used` int(11) NOT NULL COMMENT '是否使用过',
  `user_base_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`user_base_id`,`code`,`difference`),
  CONSTRAINT `user_code_ibfk_1` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息内容';

-- ----------------------------
-- Table structure for user_collection
-- ----------------------------
DROP TABLE IF EXISTS `user_collection`;
CREATE TABLE `user_collection` (
  `post_base_id` int(11) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(11) unsigned NOT NULL COMMENT '用户id',
  `create_time` int(10) DEFAULT NULL,
  `delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`post_base_id`,`user_base_id`),
  KEY `user_base_id` (`user_base_id`),
  CONSTRAINT `user_collection_ibfk_1` FOREIGN KEY (`post_base_id`) REFERENCES `post_base` (`id`),
  CONSTRAINT `user_collection_ibfk_2` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户收藏表';

-- ----------------------------
-- Table structure for user_detail
-- ----------------------------
DROP TABLE IF EXISTS `user_detail`;
CREATE TABLE `user_detail` (
  `user_base_id` int(5) unsigned NOT NULL COMMENT '用户id',
  `authorization` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '身份',
  `status` int(1) NOT NULL COMMENT '状态',
  `last_logtime` int(10) DEFAULT NULL,
  `sex` int(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `birthday` int(11) DEFAULT NULL,
  `year` varchar(4) COLLATE utf8_bin DEFAULT NULL COMMENT '年',
  `month` varchar(2) COLLATE utf8_bin DEFAULT NULL COMMENT '月',
  `day` varchar(2) COLLATE utf8_bin DEFAULT NULL COMMENT '日',
  `mail_checked` varchar(2) COLLATE utf8_bin DEFAULT NULL,
  `profile_picture` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '用户头像',
  PRIMARY KEY (`user_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户详情';

-- ----------------------------
-- Table structure for user_password
-- ----------------------------
DROP TABLE IF EXISTS `user_password`;
CREATE TABLE `user_password` (
  `user_base_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `new_password` varchar(60) COLLATE utf8_bin NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `modify_time` datetime DEFAULT NULL,
  PRIMARY KEY (`user_base_id`),
  CONSTRAINT `user_password_ibfk_1` FOREIGN KEY (`user_base_id`) REFERENCES `user_base` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=188 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户密码表，加强版';
