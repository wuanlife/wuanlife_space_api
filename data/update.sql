-- 2016/05/29
-- post.text + 5000
ALTER TABLE  `post_detail` CHANGE  `text`  `text` VARCHAR( 5000 ) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT  '内容';
-- 2016/05/30
-- add g_image and g_introduction in group_base
ALTER TABLE group_base ADD g_image VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_bin  NULL COMMENT  '图片';
ALTER TABLE group_base ADD g_introduction varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '组介绍';
-- 在group_detail添加主键
ALTER TABLE group_detail ADD PRIMARY KEY (group_base_id,user_base_id);
-- 创建post_base表
CREATE TABLE IF NOT EXISTS `post_image` (
  `id` int(11) NOT NULL COMMENT '帖子id',
  `p_image` varchar(255) DEFAULT NULL COMMENT '帖子图片',
  `delete` int(11) NOT NULL DEFAULT '0' COMMENT '删除'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='回复帖子图片';