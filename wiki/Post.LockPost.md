# post.lock_post

锁定帖子接口

## 接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/post/lock_post

请求方式：GET

参数说明：

|参数名字    |类型   |是否必须    |默认值    |范围        |说明|
|:--|:--|:--|:--|:--|:--|
|post_id    |整型   |必须         |      |最小：1     |帖子id|

附：头部添加Access-Token

## 返回说明：

|参数        |类型   |说明|
|:--|:--|:--|
|code            |整型   |操作码，2表示取消锁定帖子成功，1表示锁定帖子成功，0表示锁定帖子失败|
|msg             |字符串  |提示信息|

## 示例：

将帖子id为1的帖子锁定

http://localhost/wuanlife_api/index.php/post/lock_post?post_id=1

    JSON:
    {
        "ret": 200,
        "data": {
            "code": 1
        },
        "msg": "锁定帖子成功!"
    }
