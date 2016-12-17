-- 2016/12/13   11:24
-- 新增用户点赞表
-- user:小超
CREATE TABLE IF NOT EXISTS `post_approved` (
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `floor` int(11) NOT NULL,
  `approved` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='帖子点赞表';
ALTER TABLE post_approved
ADD PRIMARY KEY (user_id,post_id,floor);




ALTER TABLE `post_detail` ADD `replyFloor` INT DEFAULT NULL COMMENT '帖子内被回复的人的楼层';