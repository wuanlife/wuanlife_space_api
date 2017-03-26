# post.delete_post

删除帖子接口

## 接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/Post/delete_post

请求方式：GET

参数说明：

|参数名字    |类型   |是否必须    |默认值    |范围        |说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |字符串   |必须    |           |最小：1     |用户id|
|post_id    |字符串   |必须         |      |最小：1     |帖子id

## 返回说明：

|参数        |类型   |说明|
|:--|:--|:--|
|code            |整型   |操作码，1表示删帖成功，0表示删帖失败|
|msg            |字符串  |提示信息|

## 示例：

将帖子id为1的帖子删除

http://localhost/wuanlife_api/index.php/Post/delete_post?post_id=1&user_id=58

    JSON:
    {
        "ret": 200,
        "data": {
            "code": 1
        },
        "msg": "成功删除帖子"
    }
