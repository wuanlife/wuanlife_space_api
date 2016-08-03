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
-- add getpasstime and regtime
ALTER TABLE  `user_base` ADD  `getpasstime` INT NULL COMMENT '验证码时间戳',
ADD  `regtime` INT NULL COMMENT '注册时间';
-- 2016/08/02 18:14
-- add 
--ALTER TABLE `user_base`  ADD `verification` BOOLEAN NOT NULL COMMENT '邮箱验证';已有字段，无需添加