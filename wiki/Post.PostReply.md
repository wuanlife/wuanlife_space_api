#post.post_reply

帖子的回复-单个帖子的回复操作

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Post.PostReply

请求方式：POST

参数说明

|参数  |  类型|  是否必须|    默认值 |   范围     | 说明|
|:--|:--|:--|:--|:--|:--|:--|
|post_id|   整型  |  必须     |       |      |        帖子ID|
|p_text      |  字符串|  必须     |      |   |          回复内容|
|user_id    | 整型 | 必须     |         |  |        回帖人ID|
|reply_floor    | 整型 | 可选     |         |  |        帖子内被回复的人的楼层|

##返回说明

|返回字段         |   类型      |  说明|
|:--|:--|:--|
|post_id   |    整型       |帖子ID|
|user_id     |   整型   |    回帖人ID|
|reply_id        |     整型|被回帖人ID，为NULL代表回复楼主|
|p_text            |    字符串    | 回复内容|
|p_floor      |         整型     |  自己的回复所在的楼层|
|create_time     |     日期  |     回帖时间|
|user_name   |string|    回帖人昵称|
|reply_user_name     |     字符串  |被回帖人昵称，为NULL代表回复楼主|
|reply_page    |     整型  |     帖子内回复的人的帖子所在的页数|
|page|整型|回复内容所在的页码|

##示例

回复帖子id=2楼层为5的的帖子

http://dev.wuanlife.com:800/?service=Post.PostReply&post_id=1&text=1&user_id=6&replyfloor=4

    JSON:
    {
        "ret": 200,
        "data": {
            "replyid": "9",
            "text": "1",
            "floor": 51,
            "createTime": "2016-12-17 19:15:25",
            "replyFloor": 4,
            "nickname": "azusa",
            "user_id": "6",
            "postID": 1,
            "replynickname": "123",
            "page": 2,
            "replyPage": 1
        },
        "msg": ""
    }
