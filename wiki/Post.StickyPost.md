# post.sticky_post

置顶帖子接口

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Post.StickyPost

请求方式：POST

参数说明：

|参数名字    |类型   |是否必须    |默认值    |范围        |说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |字符串   |必须     |          |最小：1     |用户id|
|post_id    |字符串   |必须        |       |最小：1     |帖子id|

## 返回说明：

|参数        |类型   |说明|
|:--|:--|:--|
|code            |整型   |操作码，1表示置顶帖子成功，0表示置顶帖子失败|
|msg             |字符串  |提示信息|

## 示例：

置顶帖子id为1的帖子

http://dev.wuanlife.com:800/?service=Post.StickyPost

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "msg": "操作成功"
    },
    "msg": ""
    }
