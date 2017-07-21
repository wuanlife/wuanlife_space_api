# 仅适用于1.1.6迁移1.2.0版本更新 修复帖子删除问题  仅执行一次
UPDATE post_detail,
 post_base
SET post_detail.`delete` = post_base.`delete`
WHERE
	post_detail.post_base_id = post_base.id
AND post_detail.floor = 1;