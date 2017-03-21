# post.delete_post_reply

删除帖子回复接口

## 接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/Post/delete_post_reply

请求方式：GET

参数说明：

|参数名字    |类型   |是否必须    |默认值    |范围        |说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |整型   |必须    |           |最小：1     |用户id|
|post_id    |整型   |必须         |      |最小：1     |帖子id|
|floor    |整型   |必须         |      |最小：1     |帖子楼层|

## 返回说明：

|参数        |类型   |说明|
|:--|:--|:--|
|code            |整型   |操作码，1表示删帖成功，0表示删帖失败|
|msg             |字符串  |提示信息|

## 示例：

将帖子id为1的帖子删除

http://localhost/wuanlife_api/index.php/Post/delete_post_reply?user_id=58&post_id=1&floor=1

    JSON:
    {
        "ret": 200,
        "data": {
            "code": 1
        },
        "msg": "成功删除帖子回复"
    }
