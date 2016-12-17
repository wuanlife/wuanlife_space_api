#Post.GetPostReply

帖子详情-回帖内容

##接口调用请求说明

接口URL：http://apihost/?service=Post.GetPostReply

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--|:--|
|post_id|int	|必须|	-	|帖子ID|
|user_id|int    |必须|    -   |用户ID|
|pn	|int	|不必须|	1|	第几页|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|reply.replyFloor|int  |帖子内被回复的人的楼层|
|reply.text|string	|回复内容|
|reply.user_id	|int|	回帖人ID|
|reply.nickname	|string|	回帖人昵称|
|reply.replyid	|int|	被回复人ID，为NULL代表回复楼主|
|reply.replynickname	|string|被回复人昵称，为NULL代表回复楼主|
|reply.createTime|	date|	回帖时间|
|reply.floor|  int|   帖子楼层|
|reply.approved|	int	|是否点赞(0未点赞，1已点赞)|
|reply.approvednum|	int	|点赞数|
|reply.deleteRight|  int|   删除权限（1为有此权限）|
|postID|		int	|帖子ID|
|replyCount	|int|回帖数|
|pageCount	|int	|总页数|
|currentPage	|int|	当前页|

##示例

显示帖子ID为1用户id为1的第2页回复

http://dev.wuanlife.com:800/?service=Post.GetPostReply&post_id=1&user_id=1&pn=2

    JSON:
    {
        "ret": 200,
        "data": {
            "postID": 1,
            "replyCount": 46,
            "pageCount": 2,
            "currentPage": 2,
            "reply": [
                {
                    "replyFloor": "2",
                    "text": "1",
                    "user_id": "6",
                    "nickname": "azusa",
                    "replyid": "9",
                    "replynickname": "123",
                    "createTime": "2016-12-17 19:09:47",
                    "floor": "49",
                    "approved": "0",
                    "approvednum": "0",
                    "deleteRight": 1
                },
                {
                    "replyFloor": "2",
                    "text": "1",
                    "user_id": "6",
                    "nickname": "azusa",
                    "replyid": "9",
                    "replynickname": "123",
                    "createTime": "2016-12-17 19:14:43",
                    "floor": "50",
                    "approved": "0",
                    "approvednum": "0",
                    "deleteRight": 1
                },
                {
                    "replyFloor": "4",
                    "text": "1",
                    "user_id": "6",
                    "nickname": "azusa",
                    "replyid": "9",
                    "replynickname": "123",
                    "createTime": "2016-12-17 19:15:25",
                    "floor": "51",
                    "approved": "0",
                    "approvednum": "0",
                    "deleteRight": 1
                }
            ]
        },
        "msg": ""
    }
