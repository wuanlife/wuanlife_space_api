#Post.PostReply

帖子的回复-单个帖子的回复操作

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Post.PostReply

请求方式：POST

参数说明

|参数  |  类型|  是否必须|    默认值 |   范围     | 说明|
|:--|:--|:--|:--|:--|:--|:--|
|post_id|   整型  |  必须     |       |      |        帖子ID|
|text      |  字符串|  必须     |      |   |          回复内容|
|user_id    | 整型 | 必须     |         |  |        回帖人ID|
|replyfloor    | 整型 | 可选     |         |  |        帖子内被回复的人的楼层|

##返回说明

|返回字段         |   类型      |  说明|
|:--|:--|:--|
|post_base_id    |    整型       |帖子ID|
|user_base_id     |   整型   |    回帖人ID|
|replyid        |     整型|       帖子内被回复的人的id|
|text            |    字符串    | 回复内容|
|floor      |         整型     |  自己的回复所在的楼层|
|createTime     |     日期  |     回帖时间|
|user_base_name     |     字符串  |     回帖人名称|
|reply_user_name     |     字符串  |     帖子内被回复的人的名称|
|reply_Page    |     整型  |     帖子内被回复的人的帖子所在的页数|

##示例

回复帖子id=2楼层为5的的帖子

http://dev.wuanlife.com:800/?service=post.Postreply&post_id=25&text=666666&user_id=5&replyfloor=0

    JSON:
    {
        "ret": 200,
        "data": {
            "post_base_id": 25,
            "user_base_id": "5",
            "replyid": null,
            "text": "666666",
            "floor": 29,
            "createTime": "2016-11-24 18:20:38",
            "user_base_name": "奇奇",
            "reply_user_name": null,
            "replyPage": false
        },
        "msg": ""
    }
