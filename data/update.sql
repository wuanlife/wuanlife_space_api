-- 2016/05/29
-- post.text + 5000
ALTER TABLE  `post_detail` CHANGE  `text`  `text` VARCHAR( 5000 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT  'å†…å®¹';

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
-- è¡¨çš„ç»“æž„ `authorization`
--

CREATE TABLE IF NOT EXISTS `authorization` (
  `area_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT 'æƒé™ä½ç½®åŒºåˆ†',
  `aser_dif` varchar(2) COLLATE utf8_bin NOT NULL COMMENT 'æƒé™åŒºåˆ†',
  `note` varchar(8) COLLATE utf8_bin NOT NULL COMMENT 'è¯´æ˜Ž'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='æƒé™è¡¨';

--
-- è½¬å­˜è¡¨ä¸­çš„æ•°æ® `authorization`
--

INSERT INTO `authorization` (`area_dif`, `aser_dif`, `note`) VALUES
('01', '01', '用户-会员'),
('01', '02', '用户-管理员'),
('01', '03', '用户-总管理'),
('02', '01', '星球-创建者'),
('02', '02', '星球-管理'),
('02', '03', '星球-成员');

