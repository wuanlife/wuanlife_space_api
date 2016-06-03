-- phpMyAdmin SQL Dump
-- version phpStudy 2014
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016 �?05 �?21 �?19:06
-- 服务器版�? 5.5.47
-- PHP 版本: 5.5.30

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据�? `wuan`
--

-- --------------------------------------------------------

--
-- 表的结构 `group_base`
--

CREATE TABLE IF NOT EXISTS `group_base` (
  `id` int(4) unsigned NOT NULL AUTO_INCREMENT COMMENT '组id',
  `name` varchar(11) CHARACTER SET gbk NOT NULL COMMENT '组名',
  `g_image` varchar(255) CHARACTER SET gbk DEFAULT NULL COMMENT '组图�?,
  `g_introduction` varchar(50) CHARACTER SET gbk DEFAULT NULL COMMENT '组介�?,
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `group_detail`
--

CREATE TABLE IF NOT EXISTS `group_detail` (
  `group_base_id` int(4) unsigned NOT NULL COMMENT '组id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '成员id',
  `authorization` varchar(9) CHARACTER SET utf8 NOT NULL COMMENT '身份'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='组成员表';

-- --------------------------------------------------------

--
-- 表的结构 `post_base`
--

CREATE TABLE IF NOT EXISTS `post_base` (
  `id` int(9) unsigned NOT NULL AUTO_INCREMENT COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '发帖人id',
  `group_base_id` int(4) unsigned NOT NULL COMMENT '组id',
  `title` varchar(30) CHARACTER SET gbk NOT NULL COMMENT '标题',
  `digest` int(1) NOT NULL DEFAULT '0' COMMENT '精华',
  `sticky` int(1) NOT NULL DEFAULT '0' COMMENT '置顶',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`id`),
  KEY `user_base_id` (`user_base_id`,`group_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='主帖' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `post_detail`
--

CREATE TABLE IF NOT EXISTS `post_detail` (
  `post_base_id` int(5) unsigned NOT NULL COMMENT '帖子id',
  `user_base_id` int(5) unsigned NOT NULL COMMENT '回帖人id',
  `replyid` int(5) unsigned DEFAULT NULL COMMENT '回复的id',
  `text` varchar(5000) COLLATE utf8_bin NOT NULL COMMENT '内容',
  `floor` int(4) NOT NULL COMMENT '楼层',
  `createTime` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '发布时间',
  `delete` int(1) NOT NULL DEFAULT '0' COMMENT '删除',
  PRIMARY KEY (`post_base_id`,`floor`),
  KEY `user_base_id` (`user_base_id`,`replyid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='回复�?;

-- --------------------------------------------------------

--
-- 表的结构 `user_base`
--

CREATE TABLE IF NOT EXISTS `user_base` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `password` varchar(35) COLLATE utf8_bin NOT NULL COMMENT '密码',
  `nickname` varchar(20) COLLATE utf8_bin NOT NULL COMMENT '昵称',
  `Email` varchar(30) COLLATE utf8_bin NOT NULL COMMENT '邮箱',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nickname` (`nickname`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户表基�? AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user_detail`
--

CREATE TABLE IF NOT EXISTS `user_detail` (
  `user_base_id` int(5) unsigned NOT NULL COMMENT '用户id',
  `authorization` varchar(9) CHARACTER SET utf8 NOT NULL COMMENT '身份',
  `status` int(1) NOT NULL COMMENT '状�?,
  `lastLogTime` datetime NOT NULL COMMENT '上次登录',
  PRIMARY KEY (`user_base_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户详情';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
