--2016/08/07
--增加注册时间
ALTER TABLE `user_base`  ADD `regtime` INT NOT NULL COMMENT '注册时间';
--2016/08/31
--增加数据表存放验证码
--user:aunhappy
CREATE TABLE IF NOT EXISTS `user_code` (
  `id` int(11) NOT NULL COMMENT '用户ID',
  `getpasstime` int(11) NOT NULL COMMENT '发送验证码时间',
  `code` int(11) NOT NULL COMMENT '验证码',
  `difference` int(11) NOT NULL COMMENT '区别',
  `used` int(11) NOT NULL COMMENT '是否使用过',
) ENGINE=InnoDB DEFAULT CHARSET=gbk;
