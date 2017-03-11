#post.get_index_post

首页帖子接口-用于展示首页帖子

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/post/get_index_post/user_id/pn

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

显示第1页帖子

http://localhost/wuanlife_api/index.php/post/get_index_post

    JSON:
    {
        "ret": 200,
        "data": {
            "pageCount": 6,
            "currentPage": 6,
            "posts": [
                {
                    "post_id": "3",
                    "p_title": "sdfasd",
                    "p_text": "fasdfsd",
                    "lock": "0",
                    "create_time": "2017",
                    "user_name": "123123",
                    "group_id": "355",
                    "groupName": "一二三四五六七八九十一二三四五六七八九十",
                    "approved": "0",
                    "approvednum": "0",
                    "image": []
                },
                {
                    "post_id": "11",
                    "p_title": "1",
                    "p_text": "1",
                    "lock": "0",
                    "create_time": "2017",
                    "user_name": "xjkui",
                    "group_id": "166",
                    "groupName": "叶氏春秋",
                    "approved": "0",
                    "approvednum": "0",
                    "image": []
                }
            ]
        },
        "msg": ""
    }
