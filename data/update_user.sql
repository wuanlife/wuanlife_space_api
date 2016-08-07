--2016/08/07
--增加注册时间、验证码有效期、重置密码验证码、验证邮箱验证码
ALTER TABLE `user_base`  ADD `regtime` INT NOT NULL COMMENT '注册时间',  
ADD `getpasstime` INT NOT NULL COMMENT '验证码有效期',  
ADD `p_code` INT(5) NOT NULL DEFAULT '1' COMMENT '重置密码验证码',  
ADD `e_code` INT(5) NOT NULL DEFAULT '1' COMMENT '验证邮箱验证码';