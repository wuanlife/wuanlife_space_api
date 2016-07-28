-- 2016/05/29
-- post.text + 5000
ALTER TABLE  `post_detail` CHANGE  `text`  `text` VARCHAR( 5000 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT  '内容';

-- 2016/05/30

-- add g_image and g_introduction in group_base
ALTER TABLE `group_base` ADD `g_image` VARCHAR( 255 ) CHARACTER SET gbk DEFAULT  NULL COMMENT  '图片';
ALTER TABLE `group_base` ADD `g_introduction` varchar(50) CHARACTER SET gbk DEFAULT NULL COMMENT '组介绍';
-- 在group_detail添加主键
ALTER TABLE `group_detail` ADD PRIMARY KEY (group_base_id,user_base_id);


-- 2016/06/02
-- fix
ALTER TABLE  `user_detail` CHANGE  `authorization`  `authorization` VARCHAR( 2 ) NOT NULL COMMENT  '权限';
ALTER TABLE  `group_detail` CHANGE  `authorization`  `authorization` VARCHAR( 2 ) NOT NULL COMMENT  '权限';
--
-- 表的结构 `authorization`
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

-- 2016/07/10
-- set user admin
UPDATE `user_detail` SET `authorization`='01' WHERE `user_base_id`='1'

-- 2016/07/27
-- add user sex
ALTER TABLE `user_detail` ADD `sex` int(1) not null  DEFAULT '0' COMMENT '性别';
-- add user year
ALTER TABLE `user_detail` ADD `year` varchar(4) COMMENT '年';
-- add user month
ALTER TABLE `user_detail` ADD `month` varchar(2) COMMENT '月';
-- add user day
ALTER TABLE `user_detail` ADD `day` varchar(2) COMMENT '日';
-- add user testmail
ALTER TABLE `user_detail` ADD `mailChecked` varchar(2) not null  DEFAULT '0' COMMENT '是否验证邮箱';

-- 2016/07/28 18:24
--add getpasstime and regtime
ALTER TABLE  `user_base` ADD  `getpasstime` INT NULL ,
ADD  `regtime` INT NULL