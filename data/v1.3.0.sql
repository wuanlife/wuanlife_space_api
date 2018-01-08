
-- 鉴于MySql中外键的性能问题，
-- 本数据库设计不使用MySql提供的外键功能，
-- 所有的约束都在业务层由代码实现。

-- 用户基础表
-- 储存登陆用的基本信息，日后可扩展一张详细信息表
CREATE TABLE IF NOT EXISTS users_base
(
  id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT '用户id',
  mail VARCHAR(30) COLLATE utf8_bin NOT NULL COMMENT '用户邮箱',
  name CHAR(20) COLLATE utf8_bin NOT NULL COMMENT '用户名',
  password CHAR(32) COLLATE utf8_bin NOT NULL COMMENT '用户密码',
  create_at TIMESTAMP NOT NULL COMMENT '注册时间',
  PRIMARY KEY (id),
  KEY login_index(mail,password)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户基础信息表';

-- 用户详细信息表
-- 储存登陆用的基本信息，日后可扩展一张详细信息表
CREATE TABLE IF NOT EXISTS users_detail
(
  id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT '用户id',
  sex  TINYINT UNSIGNED NOT NULL COMMENT '性别',
  birthday DATE NOT NULL COMMENT '用户生日',
  PRIMARY KEY (id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户详细信息表';

-- 性别对应关系表
-- 储存登陆用的基本信息，日后可扩展一张详细信息表
CREATE TABLE IF NOT EXISTS sex_detail
(
  id TINYINT UNSIGNED NOT NULL COMMENT '性别标识',
  sex VARCHAR(10) COLLATE utf8_bin NOT NULL COMMENT '性别类型',
  PRIMARY KEY (id),
  KEY sex_detail_index(sex)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='性别对应关系表';

-- 用户头像表
-- 储存用户头像
CREATE TABLE IF NOT EXISTS avatar_url
(
  user_id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT '用户id',
  url VARCHAR(255) COLLATE utf8_bin NOT NULL COMMENT '图片url',
  delete_flg TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片是否已删除',
  KEY image_user_id(user_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin  COMMENT='用户头像url表';

-- 用户权限表
-- 用于实现管理员功能，
-- 因为取消了group功能，所以身份关系是唯一的，但是考虑到日后的可扩展性，
-- 此处使用 | 操作符来进行状态的叠加，使用 & 运算符进行状态的判断
CREATE TABLE IF NOT EXISTS users_auth
(
  id INT UNSIGNED NOT NULL COMMENT '用户id',
  name CHAR(20) COLLATE utf8_bin NOT NULL COMMENT '用户名',
  auth INT UNSIGNED NOT NULL COMMENT '用户权限',
  PRIMARY KEY (id),
  KEY auth(auth)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户权限表';

-- 权限对应关系表
-- 使用 << 运算符来得到不同的状态码
CREATE TABLE IF NOT EXISTS auth_detail
(
  id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT '权限id',
  indentity VARCHAR(30) COLLATE utf8_bin NOT NULL COMMENT '权限类型',
  PRIMARY KEY (id),
  UNIQUE KEY (indentity)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='权限对应关系表';

-- 文章基础信息表
-- 新增文章时，将文章的基本信息和摘要储存到该表，
-- 文章标题和全文内容储存到另一张表中
CREATE TABLE IF NOT EXISTS articles_base
(
  id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT '文章id',
  author_id INT UNSIGNED NOT NULL COMMENT '作者id',
  author_name CHAR(20) COLLATE utf8_bin NOT NULL COMMENT '作者名',
  content_digest CHAR(90) COLLATE utf8_bin NOT NULL COMMENT '文章摘要',
  update_at TIMESTAMP NOT NULL COMMENT '文章更新时间',
  create_at TIMESTAMP NOT NULL COMMENT '文章创建时间',
  PRIMARY KEY (id),
  KEY author_index(author_name)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='文章表';

-- 文章内容表
-- 将文章的标题和内容独立出来，
-- 使用一张MyIsam引擎的表储存，以便实现全文索引
CREATE TABLE IF NOT EXISTS articles_content (
  id INT UNSIGNED NOT NULL COMMENT '文章id',
  title CHAR(55) COLLATE utf8_bin NOT NULL COMMENT '文章标题', -- 是否使用CHAR有待商榷
  content TEXT COLLATE utf8_bin NOT NULL COMMENT '文章内容',
  PRIMARY KEY (id),
  FULLTEXT KEY content_index(title,content)
)ENGINE=MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='文章内容表';

-- 文章状态表
-- 使用 | 操作符来进行状态的叠加，使用 & 运算符进行状态的判断
CREATE TABLE IF NOT EXISTS articles_status
(
  id INT UNSIGNED NOT NULL COMMENT '文章id',
  status TINYINT UNSIGNED NOT NULL COMMENT '文章状态',
  create_at TIMESTAMP NOT NULL COMMENT '操作时间',
  PRIMARY KEY articles_status_index(status,id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='文章状态表';

-- 文章状态的对应关系表，
-- 使用 << 运算符来得到不同的状态码，
-- 如锁定状态为1，顶置状态为1<<1，精华状态为1<<2
CREATE TABLE IF NOT EXISTS articles_status_detail
(
  status TINYINT UNSIGNED NOT NULL COMMENT '文章状态码',
  detail VARCHAR(6) COLLATE utf8_bin NOT NULL COMMENT '状态类型',
  PRIMARY KEY (status)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='文章状态对应关系表';

-- 用户文章总数缓存表
-- 该表通过 TRIGGER 实现自动维护，避免主表的count()操作
CREATE TABLE IF NOT EXISTS users_articles_count
(
  user_id INT UNSIGNED NOT NULL COMMENT '用户id',
  count INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '用户文章总数',
  PRIMARY KEY (user_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户文章总数缓存表';

-- 文章图片 URL 表
-- 截取文章摘要时应向该表提供至多三张图片的 url 地址（如果有的话），
-- 在返回文章摘要信息时，由该表提供至多三张图片的url地址
CREATE TABLE IF NOT EXISTS image_url(
  article_id INT UNSIGNED NOT NULL COMMENT '文章id',
  url VARCHAR(255) COLLATE utf8_bin NOT NULL COMMENT '图片url',
  delete_flg TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '图片是否已删除',
  KEY image_articles_id(article_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin  COMMENT='文章图片url表';

-- 评论基础信息表
-- 储存文章评论的基本信息
CREATE TABLE IF NOT EXISTS articles_comments
(
  comment_id INT UNSIGNED AUTO_INCREMENT NOT NULL COMMENT '评论id',
  article_id INT UNSIGNED NOT NULL COMMENT '文章id',
  user_id INT UNSIGNED NOT NULL COMMENT '评论用户id',
  floor INT UNSIGNED NOT NULL COMMENT '评论所在楼层',
  create_at TIMESTAMP NOT NULL COMMENT '评论创建时间',
  PRIMARY KEY (comment_id),
  KEY (article_id),
  KEY floor_index(floor)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='评论基础信息';

-- 评论内容表
-- 将评论内容独立出来建立MyIsam表，以便建立全文索引
CREATE TABLE IF NOT EXISTS comment_contents
(
  id INT UNSIGNED NOT NULL COMMENT '评论id',
  content VARCHAR(235) COLLATE utf8_bin NOT NULL COMMENT '评论内容',
  PRIMARY KEY (id),
  FULLTEXT comment_index(content)
)ENGINE MyIsam DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='评论内容表';

-- 文章评论数缓存表
-- 通过触发器实现文章评论数缓存，以避免count()操作
CREATE TABLE IF NOT EXISTS articles_comments_count
(
  articles_id INT UNSIGNED NOT NULL COMMENT '文章id',
  count INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '评论数',
  PRIMARY KEY (articles_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='文章评论数缓存表';

-- 文章点赞表
-- 该表记录为某文章点赞的用户id
CREATE TABLE IF NOT EXISTS articles_approval
(
  article_id INT UNSIGNED NOT NULL COMMENT '文章id',
  user_id INT UNSIGNED NOT NULL COMMENT '用户id',
  PRIMARY KEY (article_id,user_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='文章点赞表';

-- 点赞数缓存表
-- 通过触发器实现文章点赞数缓存，以避免count()操作
CREATE TABLE IF NOT EXISTS articles_approval_count
(
  article_id INT UNSIGNED NOT NULL COMMENT '文章id',
  count INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '文章获得的赞数',
  PRIMARY KEY (article_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='点赞数缓存表';

-- 文章收藏表
-- 该表记录某用户收藏的所有文章
CREATE TABLE IF NOT EXISTS user_collections
(
  user_id INT UNSIGNED NOT NULL COMMENT '用户id',
  article_id INT UNSIGNED NOT NULL COMMENT '文章id',
  create_at TIMESTAMP NOT NULL COMMENT '评论创建时间',
  PRIMARY KEY (user_id,article_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='文章收藏表';

-- 用户文章收藏数缓存表
-- 通过触发器实现用户收藏数缓存，以避免count()操作
CREATE TABLE IF NOT EXISTS users_collections_count
(
  user_id INT UNSIGNED NOT NULL COMMENT '用户id',
  count INT UNSIGNED DEFAULT 0 NOT NULL COMMENT '收藏总数',
  PRIMARY KEY (user_id)
)ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin COMMENT='用户文章收藏数缓存表';

-- 触发器

DELIMITER //

-- 用户注册时，注册用户收藏数、用户文章数缓存表
CREATE TRIGGER set_users_default_buffer AFTER INSERT ON users_base FOR EACH ROW
  BEGIN
    INSERT INTO users_collections_count VALUES (NEW.id,DEFAULT );
    INSERT INTO users_articles_count VALUES (NEW.id,DEFAULT );
  END //

-- 发表文章时，注册文章评论数和点赞数缓存表,并更新用户文章数缓存表
CREATE TRIGGER set_articles_default_buffer AFTER INSERT ON articles_base FOR EACH ROW
  BEGIN
    INSERT INTO articles_comments_count VALUES (NEW.id,DEFAULT );
    INSERT INTO articles_approval_count VALUES (NEW.id,DEFAULT );
    UPDATE users_articles_count set count = count + 1 WHERE user_id = new.id;
  END //

-- 使用触发器自动缓存用户评论数
CREATE TRIGGER buffer_users_comment_count AFTER INSERT ON articles_comments FOR EACH ROW
  BEGIN
    UPDATE articles_comments_count SET count = count + 1 WHERE articles_id = NEW.article_id;
  END //

-- 使用触发器自动缓存用户收藏数
CREATE TRIGGER buffer_collections_count AFTER INSERT ON user_collections FOR EACH ROW
  BEGIN
    UPDATE users_collections_count SET count = count + 1 WHERE user_id = NEW.user_id;
  END//

-- 使用触发器自动缓存文章点赞数
CREATE TRIGGER buffer_approvals_count AFTER INSERT ON articles_approval FOR EACH ROW
  BEGIN
    UPDATE articles_approval_count set count = count + 1 WHERE article_id = new.article_id;
  END //

DELIMITER ;