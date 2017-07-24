/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50717
Source Host           : localhost:3306
Source Database       : test

Target Server Type    : MYSQL
Target Server Version : 50717
File Encoding         : 65001

Date: 2017-07-24 08:06:53
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for authorization
-- 创建时间： 2016-06-02 14:43:40
-- 最后更新： 2016-06-02 14:44:18
-- 最后检查： 2016-07-10 01:43:09
--
DROP TABLE IF EXISTS `authorization`;
CREATE TABLE `authorization` (
  `area_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限位置区分',
  `aser_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限区分',
  `note` varchar(8) COLLATE utf8_bin NOT NULL COMMENT '说明'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='权限表';

-- ----------------------------
-- Records of authorization
-- ----------------------------

INSERT INTO `authorization` (`area_dif`, `aser_dif`, `note`) VALUES
('01', '01', '用户-会员'),
('01', '02', '用户-管理员'),
('01', '03', '用户-总管理'),
('02', '01', '星球-创建者'),
('02', '02', '星球-管理'),
('02', '03', '星球-成员');

-- ----------------------------
-- Table structure for group_base
-- 创建时间： 2016-09-17 12:44:58
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组表';

-- ----------------------------
-- Records of group_base
-- ----------------------------

-- ----------------------------
-- Table structure for group_detail
-- ----------------------------
DROP TABLE IF EXISTS `group_detail`;
CREATE TABLE `group_detail` (
  `group_base_id` int(4) unsigned NOT NULL COMMENT '组id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '成员id',
  `authorization` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限',
  PRIMARY KEY (`group_base_id`,`user_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组成员表';

-- ----------------------------
-- Records of group_detail
-- ----------------------------

-- ----------------------------
-- Table structure for message_base
-- 创建时间： 2016-09-24 07:03:21
-- ----------------------------
DROP TABLE IF EXISTS `message_base`;
CREATE TABLE `message_base` (
  `code` varchar(4) COLLATE utf8_bin NOT NULL COMMENT '消息码',
  `type` int(2) NOT NULL COMMENT '消息类型',
  `content` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '消息内容',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息模板表';

-- ----------------------------
-- Records of message_base
-- ----------------------------
INSERT INTO `message_base` (`code`, `type`, `content`) VALUES
('0001', 1, '{0}申请加入{1}星球。'),
('0002', 2, '{0}同意你加入{1}星球。'),
('0003', 2, '你申请加入{1}星球已被{0}拒绝。'),
('0004', 3, '{0}已将你从{1}中移除'),
('0005', 3, '{0}已从你的{1}中退出'),
('0006', 3, '{0}已加入你的{1}'),
('0007', 3, '{0}回复我的主题{1}');


-- ----------------------------
-- Table structure for message_detail
-- 创建时间： 2016-09-24 07:03:21
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
-- Records of message_detail
-- ----------------------------

-- ----------------------------
-- Table structure for message_text
-- ----------------------------
DROP TABLE IF EXISTS `message_text`;
CREATE TABLE `message_text` (
  `message_detail_id` int(11) NOT NULL COMMENT '消息ID',
  `text` varchar(65) COLLATE utf8_bin NOT NULL COMMENT '申请信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息内容表';

-- ----------------------------
-- Records of message_text
-- 创建时间： 2016/10/17  17:29
-- ----------------------------

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
  KEY `user_base_id` (`user_base_id`,`group_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='主帖';

-- ----------------------------
-- Records of post_base
-- ----------------------------

-- ----------------------------
-- Table structure for post_detail
-- 创建时间： 2016-07-28 10:34:52
-- ----------------------------
DROP TABLE IF EXISTS `post_detail`;
CREATE TABLE `post_detail` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '回帖人id',
  `replyid` int(5) unsigned DEFAULT NULL COMMENT '回复的id',
  `text` varchar(5000) COLLATE utf8_bin NOT NULL COMMENT '内容',
  `floor` int(4) NOT NULL COMMENT '楼层',
  `createTime` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '发布时间',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`post_base_id`,`floor`),
  KEY `user_base_id` (`user_base_id`,`replyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='回复帖';

-- ----------------------------
-- Records of post_detail
-- ----------------------------

-- ----------------------------
-- Table structure for post_image
-- 创建时间： 2016-06-12 11:43:18
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
-- Records of post_image
-- ----------------------------

-- ----------------------------
-- Table structure for user_base
-- 创建时间： 2016-09-01 10:04:24
-- ----------------------------
DROP TABLE IF EXISTS `user_base`;
CREATE TABLE `user_base` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `password` varchar(35) COLLATE utf8_bin NOT NULL COMMENT '密码',
  `nickname` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '昵称',
  `Email` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '邮箱',
  `regtime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表基本';

-- ----------------------------
-- Records of user_base
-- ----------------------------

-- ----------------------------
-- Table structure for user_code
-- 创建时间： 2016-09-01 10:33:40
-- ----------------------------
DROP TABLE IF EXISTS `user_code`;
CREATE TABLE `user_code` (
  `getpasstime` int(11) NOT NULL COMMENT '发送验证码时间',
  `code` int(11) NOT NULL COMMENT '验证码',
  `difference` int(11) NOT NULL COMMENT '区别',
  `used` int(11) NOT NULL COMMENT '是否使用过',
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息内容';

-- ----------------------------
-- Records of user_code
-- ----------------------------

-- ----------------------------
-- Table structure for user_collection
-- 创建时间： 2016-11-15 17:29:10
-- ----------------------------
DROP TABLE IF EXISTS `user_collection`;
CREATE TABLE `user_collection` (
  `post_base_id` int(11) NOT NULL COMMENT '帖子id',
  `user_base_id` int(11) NOT NULL COMMENT '用户id',
  `createTime` int(10) NOT NULL COMMENT '收藏时间',
  `delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`post_base_id`,`user_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户收藏表';

-- ----------------------------
-- Records of user_collection
-- ----------------------------

-- ----------------------------
-- Table structure for user_detail
-- 创建时间： 2016-07-28 10:37:15
-- ----------------------------
DROP TABLE IF EXISTS `user_detail`;
CREATE TABLE `user_detail` (
  `user_base_id` int(5) unsigned NOT NULL COMMENT '用户id',
  `authorization` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '身份',
  `status` int(1) NOT NULL COMMENT '状态',
  `lastLogTime` int(10) NOT NULL COMMENT '上次登录',
  `sex` int(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `year` varchar(4) COLLATE utf8_bin DEFAULT NULL COMMENT '年',
  `month` varchar(2) COLLATE utf8_bin DEFAULT NULL COMMENT '月',
  `day` varchar(2) COLLATE utf8_bin DEFAULT NULL COMMENT '日',
  `mailChecked` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '是否验证邮箱',
  `profile_picture` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '用户头像',
  PRIMARY KEY (`user_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户详情';

-- ----------------------------
-- Records of user_detail
-- ----------------------------
