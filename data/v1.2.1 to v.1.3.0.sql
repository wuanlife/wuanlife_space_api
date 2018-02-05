
-- 转移用户数据
INSERT INTO wuan_api_new.users_base
  SELECT id,email,nickname as name,`password`,FROM_UNIXTIME(regtime) as create_at FROM wuan_api_old.user_base
  WHERE id > 10;
INSERT INTO wuan_api_new.users_base
  SELECT id,email,nickname as name,`password`,'1970-01-01 08:00:01' as create_at FROM wuan_api_old.user_base
  WHERE id <= 10;

-- 增加用户头像数据
INSERT INTO wuan_api_new.avatar_url
  SELECT id,'default_url',0 FROM wuan_api_old.user_base;

-- 转移用户详细数据
INSERT INTO wuan_api_new.users_detail
  SELECT id, 3,'1970-01-01' as birthday FROM wuan_api_old.user_base
  WHERE id < 10;

INSERT INTO wuan_api_new.users_detail
  SELECT user_base_id as id, sex,concat(year,'-',month,'-',day) as birthday FROM wuan_api_old.user_detail
  WHERE year > 0 AND day > 0 AND month > 0 AND user_base_id > 9;

INSERT INTO wuan_api_new.users_detail
  SELECT user_base_id as id, sex,'1970-01-01' as birthday FROM wuan_api_old.user_detail
  WHERE user_base_id NOT IN (SELECT id FROM wuan_api_new.users_detail) AND user_base_id > 9;

-- 修正脏数据
UPDATE wuan_api_old.post_detail SET create_time = '2017-12-12 00:00:00' WHERE create_time = '1497174485';

-- 转移文章基础数据
INSERT INTO wuan_api_new.articles_base
  SELECT
    post_base.id as id,
    post_base.user_base_id as author_id,
    user_base.nickname as author_name,
    ifnull(post_detail.text,'') as content_digest,
    post_detail.create_time as update_at,
    post_detail.create_time as create_at
  FROM
    (wuan_api_old.post_base
      LEFT JOIN wuan_api_old.user_base
        ON user_base_id = user_base.id)
    LEFT JOIN wuan_api_old.post_detail
      ON post_base.id = post_detail.post_base_id AND floor = 1;

-- 转移文章详细数据
INSERT INTO wuan_api_new.articles_content
  SELECT
    id, post_base.title, post_detail.text
  FROM
    wuan_api_old.post_base INNER JOIN wuan_api_old.post_detail
      ON post_base.id = post_detail.post_base_id AND floor = 1;

-- 添加文章状态数据
INSERT INTO wuan_api_new.articles_status
  SELECT id,0 AS status,create_at FROM wuan_api_new.articles_base;

-- 修正脏数据
UPDATE wuan_api_new.users_detail SET sex = 2 WHERE sex = 3;