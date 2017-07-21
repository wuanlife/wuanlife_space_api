-- phpMyAdmin SQL Dump
-- version 4.4.15.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: 2016-10-06 11:43:23
-- 服务器版本： 5.5.47-MariaDB
-- PHP Version: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `wuan`
--
CREATE DATABASE IF NOT EXISTS `wuan` DEFAULT CHARACTER SET utf8 COLLATE utf8_bin;
USE `wuan`;

-- --------------------------------------------------------

--
-- 表的结构 `authorization`
--
-- 创建时间： 2016-06-02 14:43:40
-- 最后更新： 2016-06-02 14:44:18
-- 最后检查： 2016-07-10 01:43:09
--

CREATE TABLE IF NOT EXISTS `authorization` (
  `area_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限位置区分',
  `aser_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限区分',
  `note` varchar(8) COLLATE utf8_bin NOT NULL COMMENT '说明'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='权限表';

--
-- 转存表中的数据 `authorization`
--

INSERT INTO `authorization` (`area_dif`, `aser_dif`, `note`) VALUES
('01', '01', '用户-会员'),
('01', '02', '用户-管理员'),
('01', '03', '用户-总管理'),
('02', '01', '星球-创建者'),
('02', '02', '星球-管理'),
('02', '03', '星球-成员');

-- --------------------------------------------------------

--
-- 表的结构 `group_base`
--
-- 创建时间： 2016-09-17 12:44:58
--

CREATE TABLE IF NOT EXISTS `group_base` (
  `id` int(4) unsigned NOT NULL COMMENT '组id',
  `name` varchar(21) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '组名',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `g_image` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '组图片',
  `g_introduction` varchar(50) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL COMMENT '组介绍',
  `private` int(1) NOT NULL DEFAULT '0' COMMENT '私密星球'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组表';

--
-- 表的结构 `group_detail`
--
-- 创建时间： 2016-06-02 14:38:39
--

CREATE TABLE IF NOT EXISTS `group_detail` (
  `group_base_id` int(4) unsigned NOT NULL COMMENT '组id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '成员id',
  `authorization` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '权限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组成员表';


--
-- 表的结构 `message_base`
--
-- 创建时间： 2016-09-24 07:03:21
--

CREATE TABLE IF NOT EXISTS `message_base` (
  `code` varchar(4) COLLATE utf8_bin NOT NULL COMMENT '消息码',
  `type` int(2) NOT NULL COMMENT '消息类型',
  `content` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '消息内容'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息模板表';

--
-- 转存表中的数据 `message_base`
--

INSERT INTO `message_base` (`code`, `type`, `content`) VALUES
('0001', 1, '{0}申请加入{1}星球。'),
('0002', 2, '{0}同意你加入{1}星球。'),
('0003', 2, '你申请加入{1}星球已被{0}拒绝。');

-- --------------------------------------------------------

--
-- 表的结构 `message_detail`
--
-- 创建时间： 2016-09-24 07:03:21
--

CREATE TABLE IF NOT EXISTS `message_detail` (
  `message_id` int(5) NOT NULL COMMENT '消息id',
  `message_base_code` varchar(4) COLLATE utf8_bin NOT NULL COMMENT '消息码',
  `user_base_id` int(5) NOT NULL COMMENT '用户id',
  `id_1` int(5) NOT NULL COMMENT '申请人或创建人id',
  `id_2` int(5) NOT NULL COMMENT '星球id',
  `createTime` int(10) NOT NULL COMMENT '创建时间',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '消息的状态'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户消息表';

--
-- 表的结构 `post_base`
--
-- 创建时间： 2016-09-17 12:44:58
--

CREATE TABLE IF NOT EXISTS `post_base` (
  `id` int(9) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '发帖人id',
  `group_base_id` int(4) unsigned NOT NULL COMMENT '组id',
  `title` varchar(61) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT '标题',
  `digest` int(1) NOT NULL DEFAULT '0' COMMENT '精华',
  `sticky` int(1) NOT NULL DEFAULT '0' COMMENT '置顶',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  `lock` int(1) NOT NULL DEFAULT '0' COMMENT '锁定帖子'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='主帖';


--
-- 表的结构 `post_detail`
--
-- 创建时间： 2016-07-28 10:34:52
--

CREATE TABLE IF NOT EXISTS `post_detail` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '回帖人id',
  `replyid` int(5) unsigned DEFAULT NULL COMMENT '回复的id',
  `text` varchar(5000) COLLATE utf8_bin NOT NULL COMMENT '内容',
  `floor` int(4) NOT NULL COMMENT '楼层',
  `createTime` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '发布时间',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='回复帖';

--
-- 表的结构 `post_image`
--
-- 创建时间： 2016-06-12 11:43:18
--

CREATE TABLE IF NOT EXISTS `post_image` (
  `post_base_id` int(11) NOT NULL COMMENT '帖子id',
  `post_image_id` int(11) NOT NULL COMMENT '图片id',
  `p_image` varchar(255) COLLATE utf8_bin DEFAULT NULL COMMENT '帖子图片',
  `delete` int(11) NOT NULL DEFAULT '0' COMMENT '删除'
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='?ظ?????ͼƬ';

--
-- 表的结构 `user_base`
--
-- 创建时间： 2016-09-01 10:04:24
--

CREATE TABLE IF NOT EXISTS `user_base` (
  `id` int(5) unsigned NOT NULL COMMENT '用户id',
  `password` varchar(35) COLLATE utf8_bin NOT NULL COMMENT '密码',
  `nickname` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '昵称',
  `Email` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '邮箱',
  `regtime` int(11) DEFAULT NULL
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表基本';


--
-- 表的结构 `user_code`
--
-- 创建时间： 2016-09-01 10:33:40
--

CREATE TABLE IF NOT EXISTS `user_code` (
  `getpasstime` int(11) NOT NULL COMMENT '发送验证码时间',
  `code` int(11) NOT NULL COMMENT '验证码',
  `difference` int(11) NOT NULL COMMENT '区别',
  `used` int(11) NOT NULL COMMENT '是否使用过',
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='消息内容';

-- --------------------------------------------------------

--
-- 表的结构 `user_detail`
--
-- 创建时间： 2016-07-28 10:37:15
--

CREATE TABLE IF NOT EXISTS `user_detail` (
  `user_base_id` int(5) unsigned NOT NULL COMMENT '用户id',
  `authorization` varchar(2) COLLATE utf8_bin NOT NULL COMMENT '身份',
  `status` int(1) NOT NULL COMMENT '状态',
  `lastLogTime` datetime NOT NULL COMMENT '上次登录',
  `sex` int(1) NOT NULL DEFAULT '0' COMMENT '性别',
  `year` varchar(4) COLLATE utf8_bin DEFAULT NULL COMMENT '年',
  `month` varchar(2) COLLATE utf8_bin DEFAULT NULL COMMENT '月',
  `day` varchar(2) COLLATE utf8_bin DEFAULT NULL COMMENT '日',
  `mailChecked` varchar(2) COLLATE utf8_bin NOT NULL DEFAULT '0' COMMENT '是否验证邮箱'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户详情';

-- --------------------------------------------------------

--
-- 表的结构 `message_text`
--
-- 创建时间： 2016/10/17  17:29
--
-- user:小超

CREATE TABLE IF NOT EXISTS `message_text` (
    `message_detail_id` INT (11) NOT NULL COMMENT '消息ID',
    `text` VARCHAR (65) CHARACTER
SET utf8 COLLATE utf8_bin NOT NULL COMMENT '申请信息'
) ENGINE = INNODB CHARACTER
SET utf8 COLLATE utf8_bin COMMENT = '消息内容表';

-- --------------------------------------------------------

--
-- 表的结构 `user_collection`
--
-- 创建时间： 2016/11/15   17:29
--
-- user:小超

CREATE TABLE IF NOT EXISTS `user_collection` (
	`post_base_id` INT (11) NOT NULL COMMENT '帖子id',
    `user_base_id` INT (11) NOT NULL COMMENT '用户id',
	`createTime` INT (10) NOT NULL COMMENT '收藏时间',
	`delete` TINYINT (1) NOT NULL DEFAULT '0'  COMMENT '是否删除',
	PRIMARY KEY (`post_base_id`,`user_base_id`)
) ENGINE = INNODB DEFAULT CHARSET = utf8 COLLATE = utf8_bin COMMENT = '用户收藏表' AUTO_INCREMENT = 1;


--
-- Indexes for dumped tables
--

--
-- Indexes for table `group_base`
--
ALTER TABLE `group_base`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `group_detail`
--
ALTER TABLE `group_detail`
  ADD PRIMARY KEY (`group_base_id`,`user_base_id`);

--
-- Indexes for table `message_base`
--
ALTER TABLE `message_base`
  ADD PRIMARY KEY (`code`);

--
-- Indexes for table `message_detail`
--
ALTER TABLE `message_detail`
  ADD PRIMARY KEY (`message_id`);

--
-- Indexes for table `post_base`
--
ALTER TABLE `post_base`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_base_id` (`user_base_id`,`group_base_id`);

--
-- Indexes for table `post_detail`
--
ALTER TABLE `post_detail`
  ADD PRIMARY KEY (`post_base_id`,`floor`),
  ADD KEY `user_base_id` (`user_base_id`,`replyid`);

--
-- Indexes for table `post_image`
--
ALTER TABLE `post_image`
  ADD PRIMARY KEY (`post_image_id`,`post_base_id`);

--
-- Indexes for table `user_base`
--
ALTER TABLE `user_base`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nickname` (`nickname`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `user_detail`
--
ALTER TABLE `user_detail`
  ADD PRIMARY KEY (`user_base_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `group_base`
--
ALTER TABLE `group_base`
  MODIFY `id` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '组id',AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `message_detail`
--
ALTER TABLE `message_detail`
  MODIFY `message_id` int(5) NOT NULL AUTO_INCREMENT COMMENT '消息id',AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `post_base`
--
ALTER TABLE `post_base`
  MODIFY `id` int(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '帖子id',AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `post_image`
--
ALTER TABLE `post_image`
  MODIFY `post_image_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '图片id',AUTO_INCREMENT=1;
--
-- AUTO_INCREMENT for table `user_base`
--
ALTER TABLE `user_base`
  MODIFY `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',AUTO_INCREMENT=1;
  
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
	('0007','3','{0}回复我的主题{1}');