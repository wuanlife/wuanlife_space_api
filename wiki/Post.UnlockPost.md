# post.unlock_post

解锁帖子接口

## 接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/Post/unlock_post

请求方式：POST

参数说明：

|参数名字    |类型   |是否必须    |默认值    |范围        |说明|
|:--|:--|:--|:--|:--|:--|
|token|字符串|必须|-|-|jwt字符串，包含下列两个参数|
|user_id    |整型   |必须    |           |最小：1     |用户id，包含于token|
|post_id    |整型   |必须         |      |最小：1     |帖子id，包含于token|

## 返回说明：

|参数        |类型   |说明|
|:--|:--|:--|
|code            |整型   |操作码，1表示解锁帖子成功，0表示解锁帖子失败|
|msg            |字符串  |提示信息|

##示例：

将帖子id为1的帖子解锁

http://localhost/wuanlife_api/index.php/Post/unlock_post?post_id=1

    JSON:
    {
        "ret": 200,
        "data": {
            "code": 1
        },
        "msg": "解除锁定帖子成功!"
    }
