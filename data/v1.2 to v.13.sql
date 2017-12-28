
-- 转移用户数据
INSERT INTO wuan_api_new.users_base
    SELECT id,email,nickname as name,`password`,regtime as create_at FROM wuan_api_old.user_base;

-- 转移文章基础数据
INSERT INTO wuan_api_new.articles_base
    SELECT
      post_base.id,
      post_base.user_base_id as author_id,
      nickname as author_name,
      ' ' as content_digest,
      post_content.modify_time as update_at,
      post_content.create_time as create_at
    FROM
      (wuan_api_old.post_base
      INNER JOIN wuan_api_old.user_base
        ON user_base_id = user_base.id)
      INNER JOIN wuan_api_old.post_content
        ON post_base.id = post_content.post_base_id;

-- 转移文章详细数据
INSERT INTO wuan_api_new.articles_content
    SELECT
      post_base_id as id,
      post_base.title,
      post_content.content
    FROM
      wuan_api_old.post_content
      INNER JOIN wuan_api_old.post_base
        ON post_content.post_base_id = post_base.id;