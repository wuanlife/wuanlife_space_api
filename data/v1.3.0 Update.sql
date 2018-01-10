
-- 设置文章状态对应关系
INSERT INTO articles_status_detail VALUES (1, '锁定'), (2, '删除');

-- 为超级管理员占坑(导入老数据后已不需要占坑)
# INSERT INTO users_base VALUES (1, '11111', '11111', md5(123456), DEFAULT);
# INSERT INTO avatar_url VALUES (1, '11111', 0);
# INSERT INTO users_detail VALUES (1, 3, '0000-00-00');

-- 添加性别对应关系
INSERT INTO sex_detail VALUES (0, '女'), (1, '男'), (2, '不想透露');

-- 添加管理员对应关系
INSERT INTO auth_detail VALUES (1, '超级管理员'), (2, '管理员');
