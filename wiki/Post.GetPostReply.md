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
|reply.text|string	|内容|
|reply.nickname	|string|	发帖人|
|reply.createTime|	date|	发帖时间|
|reply.floor|  int|   帖子楼层|
|reply.deleteRight|  int|   删除权限|
|postID|		int	|帖子ID|
|replyCount	|int|回帖数|
|pageCount	|int	|总页数|
|currentPage	|int|	当前页|
|deleteRight    |int|   删除权限（有此权限可忽略reply.deleteRight，1为有此权限）|
##示例

显示帖子ID为1的第1页回复

http://apihost/?service=Post.GetPostReply&post_id=1&pn=1

    JSON:
    {
        "ret": 200,
        "data": {
            "postID": 1,
            "replyCount": 36,
            "pageCount": 2,
            "currentPage": 1,
            "reply": [
                {
                    "text": "666666",
                    "nickname": "123",
                    "createTime": "2016-05-30 14:26:54",
                    "floor": "4",
                    "deleteRight": 0
                },
                {
                    "text": "12:37",
                    "nickname": "12222",
                    "createTime": "2016-06-15 12:40:01",
                    "floor": "28",
                    "deleteRight": 1
                },
                {
                    "text": "12:37",
                    "nickname": "12222",
                    "createTime": "2016-06-15 12:40:02",
                    "floor": "29",
                    "deleteRight": 1
                },
                {
                    "text": "12:37",
                    "nickname": "12222",
                    "createTime": "2016-06-15 12:40:05",
                    "floor": "33",
                    "deleteRight": 1
                },
                {
                    "text": "12:37",
                    "nickname": "12222",
                    "createTime": "2016-06-15 12:40:06",
                    "floor": "34",
                    "deleteRight": 1
                },
                {
                    "text": "12:37",
                    "nickname": "12222",
                    "createTime": "2016-06-15 12:40:07",
                    "floor": "35",
                    "deleteRight": 1
                }
            ],
            "deleteRight": 1
        },
        "msg": ""
    }
