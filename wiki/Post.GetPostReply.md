#Post.GetPostReply

帖子详情-回帖内容

##接口调用请求说明

接口URL：http://apihost/?service=Post.GetPostReply

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--|:--|
|post_id|int	|必须|	-	|帖子ID|
|pn	|int	|不必须|	1|	第几页|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|reply.text|string	|内容|
|reply.nickname	|string|	发帖人|
|reply.createTime|	date|	发帖时间|
|postID|		int	|帖子ID|
|replyCount	|int|回帖数|
|pageCount	|int	|总页数|
|currentPage	|int|	当前页|
##示例

显示帖子ID为1的第1页回复

http://apihost/?service=Post.GetPostReply&post_id=1&pn=1

    JSON:
    {
    "ret": 200,
    "data": {
        "reply": [
            {
                "text": "2回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:21:00"
            },
            {
                "text": "3回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:22:00"
            },
            {
                "text": "4回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:23:00"
            },
            {
                "text": "5回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:24:00"
            },
            {
                "text": "6回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:25:00"
            },
            {
                "text": "7回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:26:00"
            },
            {
                "text": "8回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:27:00"
            },
            {
                "text": "9回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:28:00"
            },
            {
                "text": "10回复回复回复",
                "nickname": "陶陶1",
                "createTime": "2016-04-06 00:29:00"
            }
        ],
        "postID": 1,
        "replyCount": 19,
        "pageCount": 3,
        "currentPage": 1
    },
    "msg": ""
    }
