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
|postID    |    整型       |帖子ID|
|user_id     |   整型   |    回帖人ID|
|replyid        |     整型|被回帖人ID，为NULL代表回复楼主|
|text            |    字符串    | 回复内容|
|floor      |         整型     |  自己的回复所在的楼层|
|createTime     |     日期  |     回帖时间|
|nickname   |string|    回帖人昵称|
|replynickname     |     字符串  |被回帖人昵称，为NULL代表回复楼主|
|reply_Page    |     整型  |     帖子内回复的人的帖子所在的页数|
|page|整型|回复内容所在的页码|

##示例

回复帖子id=2楼层为5的的帖子

http://dev.wuanlife.com:800/?service=Post.PostReply&post_id=1&text=1&user_id=6

    JSON:
    {
        "ret": 200,
        "data": {
            "replyid": null,
            "text": "1",
            "floor": 44,
            "createTime": "2016-11-26 17:25:11",
            "nickname": "azusa",
            "user_id": "6",
            "postID": 1,
            "replynickname": null,
            "page": 2,
            "replyPage": false
        },
        "msg": ""
    }
