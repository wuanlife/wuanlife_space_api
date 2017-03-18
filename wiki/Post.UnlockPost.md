# post.unlock_post

解锁帖子接口

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/post/unlock_post

请求方式：POST

参数说明：

|参数名字    |类型   |是否必须    |默认值    |范围        |说明|
|:--|:--|:--|:--|:--|:--|
|post_id    |整型   |必须         |      |             |帖子id|
|user_id    |整型   |必须         |      |             |用户id|

## 返回说明：

|参数        |类型   |说明|
|:--|:--|:--|
|code            |整型   |操作码，1表示解锁帖子成功，0表示解锁帖子失败|
|msg            |字符串  |提示信息|

##示例：

将帖子id为1的帖子解锁

http://dev.wuanlife.com:800/post/unlock_post

    JSON:
    {
        "ret": 200,
        "data": {
            "code": 0,
            "re": "操作成功"
        },
        "msg": ""
    }
