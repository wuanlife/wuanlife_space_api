# post.approve_post

点赞帖子及其回复

## 接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/Post/approve_post

请求方式：GET

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id|   整型| 必须     |-|  最小：1  |  用户ID|
|post_id|   整型| 必须     |-|  最小：1  |  帖子ID|
|floor|   整型|可选|默认：1| 最小：1  |  帖子楼层|

## 返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示操作成功，0表示操作失败|
|msg |字符串 |提示信息|


## 示例

给帖子id=1的帖子点赞

http://localhost/wuanlife_api/index.php/Post/approve_post?user_id=58&post_id=1&floor=1

    JSON：
    {
        "ret": 200,
        "data": {
            "code": 1,
            "msg": "点赞成功"
        },
        "msg": null
    }
