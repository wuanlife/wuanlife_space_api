#Post.GetMyGroupPost

我的星球页面帖子显示

##接口调用请求说明

接口URL：http://apihost/?service=Post.GetMyGroupPost

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--|:--|
|group_id |int|   必须| -|  星球ID|
|user_id	|int|	可选|	-|	用户ID|
|pn|	int	|不必须	|1|	第几页|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|post.id	|	int	|帖子ID|
|post.title	|string	|标题|
|post.text	|string|	内容|
|post.createTime|	date|	发帖时间|
|post.nickname	|string	|发帖人|
|post.groupName	|string|	星球名称|
|pageCount	|int	|总页数|
|currentPage	|int	|当前页|
|identity    |int    |01创建者，02成员，03非成员|
|private    |int    |0否，1私密|

##示例

显示用户ID为1的第1页帖子

http://apihost/?service=Post.GetMyGroupPost&id=1&pn=1

    JSON:
    {
    "ret": 200,
    "data": {
        "posts": [
            {
                "postID": "40",
                "title": "title40",
                "text": "40texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:19:00",
                "nickname": "陶陶20",
                "groupName": "鬼扯1"
            },
            {
                "postID": "39",
                "title": "title39",
                "text": "39texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:18:00",
                "nickname": "陶陶19",
                "groupName": "鬼扯1"
            },
            {
                "postID": "38",
                "title": "title38",
                "text": "38texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:17:00",
                "nickname": "陶陶18",
                "groupName": "鬼扯1"
            },
            {
                "postID": "37",
                "title": "title37",
                "text": "37texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:16:00",
                "nickname": "陶陶17",
                "groupName": "鬼扯1"
            },
            {
                "postID": "36",
                "title": "title36",
                "text": "36texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:15:00",
                "nickname": "陶陶16",
                "groupName": "鬼扯1"
            },
            {
                "postID": "35",
                "title": "title35",
                "text": "35texttexttexttexttexttexttexttexttexttext ",
                "createTime": "2016-04-06 01:14:00",
                "nickname": "陶陶15",
                "groupName": "鬼扯1"
            }
        ],
        "pageCount": 4,
        "currentPage": 1
    },
    "msg": ""
    }
