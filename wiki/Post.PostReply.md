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
|user_id    | 字符串 | 必须     |         |  |        回帖人ID|

##返回说明

|返回字段         |   类型      |  说明|
|:--|:--|:--|
|post_base_id    |    整型       |帖子ID|
|user_base_id     |   整型   |    回帖人ID|
|replyid        |     整型|       NULL|
|text            |    字符串    | 回复内容|
|floor      |         整型     |  回复楼层|
|createTime     |     日期  |     回帖时间|

##示例

回复帖子id为25的帖子

http://dev.wuanlife.com:800/?service=post.Postreply&post_id=25&text=666666&user_id=5

    JSON:
    {
    "ret": 200,
    "data": {
        "post_base_id": 25,
        "user_base_id": "5",
        "replyid": null,
        "text": "666666",
        "floor": 4,
        "createTime": "2016-04-22 20:31:06"
    },
    "msg": ""
    }
