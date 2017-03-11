#post.get_mygroup_post

我的星球页面帖子显示

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/post/get_mygroup_post/user_id/pn

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--|:--|
|user_id	|int|	可选|	-|	用户ID|
|pn|	int	|不必须	|1|	第几页|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|post.post_id	|	int	|帖子ID|
|post.p_title	|string	|标题|
|post.p_text	|string|	内容|
|post.lock    |string|    是否被锁定 1被锁定|
|post.create_time|	date|	发帖时间|
|post.user_name	|string	|发帖人|
|post.g_name	|string|	星球名称|
|page_count	|int	|总页数|
|current_page	|int	|当前页|
|user_name   |string    |当前用户名|


##示例

显示用户ID为1的第1页帖子

http://localhost/wuanlife_api/index.php/post/get_mygroup_post/9

    JSON:
    {
        "ret": 200,
        "data": {
            "pageCount": 1,
            "currentPage": 1,
            "posts": [
                {
                    "post_id": "3",
                    "p_title": "sdfasd",
                    "p_text": "fasdfsd",
                    "lock": "0",
                    "create_time": "2017",
                    "user_name": "123123",
                    "group_id": "355",
                    "g_name": "一二三四五六七八九十一二三四五六七八九十",
                    "image": []
                },
                {
                    "post_id": "6",
                    "p_title": "cs",
                    "p_text": "cs",
                    "lock": "0",
                    "create_time": "2017",
                    "user_name": "xjkui",
                    "group_id": "29",
                    "g_name": "测试29",
                    "image": []
                }
            ],
            "user_name": "123123"
        },
        "msg": null
    }
