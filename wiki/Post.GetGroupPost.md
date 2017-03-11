#post.get_group_post

星球页面帖子显示

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/post/get_group_post/group_id/user_id/pn

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--:|:--|
|group_id|int|必须|-|星球ID|
|user_id|int|不必须|-|用户ID|
|pn|int|不必须|1|第几页|

##返回说明

|参数|类型|说明|
|:--|:--|:--|
|creator_id	|int|	星球创建者id|
|creator_name  |string|   星球创建者名称|
|group_id|int	|星球ID|
|g_name	|string|	星球名称|
|identity    |int    |01创建者 02成员 03非成员未申请 04非成员申请中|
|private    |int    |0否 1私密|
|posts.digest	|	int|	是否加精 1为加 0为不加|
|posts.p_text	|string|	内容|
|posts.create_time|	date|	发帖时间|
|posts.post_id	|	int|	帖子ID|
|posts.user_name|string	|发帖人|
|posts.sticky	|string|	是否置顶 1为置顶 0为不置顶|
|posts.lock	|int|	是否锁定 1为锁定 0为不锁定|
|page_count	|int	|总页数|
|current_page	|int	|当前页|


##示例

显示星球ID为16的帖子

http://localhost/wuanlife_api/index.php/post/get_group_post/166

    JSON:
    {
        "ret": 200,
        "data": {
            "creator_id": "46",
            "creator_name": "叶寻",
            "group_id": "166",
            "g_name": "叶氏春秋",
            "private": "0",
            "identity": "03",
            "posts": [
                {
                    "digest": "0",
                    "post_id": "16",
                    "title": "1",
                    "p_text": "1",
                    "create_time": "2017",
                    "id": "127",
                    "user_name": "xjkui",
                    "sticky": "0",
                    "lock": "0",
                    "image": []
                },
                {
                    "digest": "0",
                    "post_id": "24",
                    "title": "1",
                    "p_text": "1",
                    "create_time": "2017",
                    "id": "127",
                    "user_name": "xjkui",
                    "sticky": "0",
                    "lock": "0",
                    "image": []
                }
            ],
            "group_name": "叶氏春秋",
            "pageCount": 1,
            "currentPage": 1
        },
        "msg": null
    }
