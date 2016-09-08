-- 2016/09/08  13:07
-- 新增帖子的锁定字段
-- user:小超
ALTER TABLE `post_base`  ADD `lock` INT(1) NOT NULL DEFAULT '0' COMMENT '锁定帖子';
-- 2016/09/08  13:10
-- 新增星球的私密字段
-- user:小超
ALTER TABLE `group_base`  ADD `private` INT(1) NOT NULL DEFAULT '0' COMMENT '私密星球';
-- 2016/09/08  13:18
-- 新增消息基础表，包括消息id和消息内容
-- user:小超
CREATE TABLE IF NOT EXISTS `message_base` (
  `code` varchar(4) NOT NULL COMMENT '消息码',
  `content` varchar(30) NOT NULL COMMENT '消息内容',
  PRIMARY KEY (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `message_base`
--

INSERT INTO `message_base` (`code`, `content`) VALUES
('0001', '{0} 申请加入{1}星球。'),
('0002', '{0}同意你加入{1}星球。'),
('0003', '你申请加入{1}星球已被拒绝。');
-- 2016/09/08  13:18
-- 新增消息细节表，包括消息id、用户id、创建时间、是否已读
-- user:小超
CREATE TABLE IF NOT EXISTS `message_detail` (
  `message_base_id` int(4) NOT NULL COMMENT '消息id',
  `user_base_id` int(4) NOT NULL COMMENT '用户id',
  `id_1` int(9) NOT NULL COMMENT '申请人或回复人id',
  `id_2` int(9) NOT NULL COMMENT '星球id',
  `createTime` int(10) NOT NULL COMMENT '创建时间',
  `read` int(1) NOT NULL DEFAULT '0' COMMENT '是否已读',
  `count` int(1) NOT NULL DEFAULT '1' COMMENT '区分申请回复',
  PRIMARY KEY (`message_base_id`,`user_base_id`,`count`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
