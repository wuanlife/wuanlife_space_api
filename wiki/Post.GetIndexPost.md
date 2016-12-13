#Post.GetIndexPost

星球创建接口-用于创建星球

##接口调用请求说明

接口URL：http://apihost/?service=Post.GetIndexPost

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--|:--|
|user_id|   整型| 可选     ||           最小：1  |  用户ID|
|pn	|int|	false|	1	|第几页|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|posts.postID	|	int|	帖子ID|
|posts.title|	string|	标题|
|posts.text	|string	|内容|
|posts.createTime|	date|	发帖时间|
|posts.nickname|	string	|发帖人|
|posts.groupID|	int	|星球ID|
|posts.lock|	int	|是否锁定|
|posts.approved|	int	|是否点赞(0未点赞，1已点赞)|
|posts.approvednum|	int	|点赞数|
|posts.groupName|	string|	星球名称|
|pageCount	|int|	总页数|
|currentPage|	int	|当前页|

##示例

显示第二页帖子

http://apihost/?service=Post.GetIndexPost&pn=2

    JSON:
    {
    "ret": 200,
    "data": {
        "posts": [
            {
                "postID": "34",
                "title": "title34",
                "text": "34texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:13:00",
                "nickname": "陶陶14",
                "groupID": "1",
                "groupName": "鬼扯1"
            },
            {
                "postID": "33",
                "title": "title33",
                "text": "33texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:12:00",
                "nickname": "陶陶13",
                "groupID": "1",
                "groupName": "鬼扯1"
            },
            {
                "postID": "32",
                "title": "title32",
                "text": "32texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:11:00",
                "nickname": "陶陶12",
                "groupID": "1",
                "groupName": "鬼扯1"
            },
            {
                "postID": "31",
                "title": "title31",
                "text": "31texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:10:00",
                "nickname": "陶陶11",
                "groupID": "1",
                "groupName": "鬼扯1"
            },
            {
                "postID": "30",
                "title": "title30",
                "text": "30texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:09:00",
                "nickname": "陶陶10",
                "groupID": "1",
                "groupName": "鬼扯1"
            },
            {
                "postID": "29",
                "title": "title29",
                "text": "29texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:08:00",
                "nickname": "陶陶9",
                "groupID": "1",
                "groupName": "鬼扯1"
            }
        ],
        "pageCount": 7,
        "currentPage": 2
    },
    "msg": ""
    }
