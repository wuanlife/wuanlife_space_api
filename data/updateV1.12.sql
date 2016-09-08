-- 2016/07/10
-- set user admin
UPDATE `user_detail` SET `authorization`='01' WHERE `user_base_id`='1' ;

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
-- ALTER TABLE  `user_base` ADD  `getpasstime` INT NULL ,
-- ADD  `regtime` INT NULL

-- 2016/08/07
-- 增加注册时间
ALTER TABLE `user_base`  ADD `regtime` INT NOT NULL COMMENT '注册时间';
-- 2016/08/31
-- 增加数据表存放验证码
-- user:aunhappy
CREATE TABLE IF NOT EXISTS `user_code` (
  `id` int(11) NOT NULL COMMENT '用户ID',
  `getpasstime` int(11) NOT NULL COMMENT '发送验证码时间',
  `code` int(11) NOT NULL COMMENT '验证码',
  `difference` int(11) NOT NULL COMMENT '区别',
  `used` int(11) NOT NULL COMMENT '是否使用过'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='验证码';
