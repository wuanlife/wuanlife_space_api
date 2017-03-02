#post.get_index_post

首页帖子接口-用于展示首页帖子

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
|posts.post_id	|	int|	帖子ID|
|posts.p_title|	string|	标题|
|posts.p_text	|string	|内容|
|posts.create_time|	date|	发帖时间|
|posts.user_name|	string	|发帖人|
|posts.group_id|	int	|星球ID|
|posts.lock|	int	|是否锁定|
|posts.approved|	int	|是否点赞(0未点赞，1已点赞)|
|posts.approvednum|	int	|点赞数|
|posts.g_name|	string|	星球名称|
|page_count	|int|	总页数|
|current_page|	int	|当前页|

##示例

显示第二页帖子

http://apihost/?service=Post.GetIndexPost&pn=6&user_id=1

    JSON:
    {
        "ret": 200,
        "data": {
            "pageCount": 6,
            "currentPage": 6,
            "posts": [
                {
                    "postID": "1",
                    "title": "avhfhkakfgaukufbakfbafbalfabif",
                    "text": "2",
                    "lock": "0",
                    "createTime": "2016-06-12 17:57:58",
                    "nickname": "12222",
                    "groupID": "1",
                    "groupName": "装备2014中队和是加",
                    "approved": "1",
                    "approvednum": "1",
                    "image": []
                },
                {
                    "postID": "2",
                    "title": "午安煎饼计划Android组第48周周报",
                    "text": "",
                    "lock": "0",
                    "createTime": "2016-05-20 20:02:51",
                    "nickname": "午安网",
                    "groupID": "2",
                    "groupName": "午安网啊阿萨阿萨安师大",
                    "approved": "0",
                    "approvednum": "0",
                    "image": []
                },
                {
                    "postID": "11",
                    "title": "biaoti14",
                    "text": "6",
                    "lock": "1",
                    "createTime": "",
                    "nickname": "撒旦法斯蒂芬",
                    "groupID": "14",
                    "groupName": "sdfd",
                    "approved": "0",
                    "approvednum": "0",
                    "image": []
                },
                {
                    "postID": "12",
                    "title": "biaoti15",
                    "text": "7",
                    "lock": "0",
                    "createTime": "",
                    "nickname": "azusa",
                    "groupID": "15",
                    "groupName": "sdfddd",
                    "approved": "0",
                    "approvednum": "0",
                    "image": []
                }
            ]
        },
        "msg": ""
    }
