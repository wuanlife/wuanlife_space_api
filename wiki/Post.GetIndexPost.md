# post.get_index_post

首页帖子接口-用于展示首页帖子

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/post/get_index_post

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--|:--|
|user_id|   int| 可选 |-| 最小：1  |  用户ID|
|pn	|int|可选|	1	|第几页|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|page_count	|int|	总页数|
|current_page|	int	|当前页|
|posts.post_id	|	int|	帖子ID|
|posts.p_title|	string|	标题|
|posts.p_text	|string	|内容|
|posts.create_time|	date|	发帖时间|
|posts.lock|	int	|是否锁定（不知道这个参数干嘛用的）|
|posts.approved|	int	|是否点赞(0未点赞，1已点赞)|
|posts.approved_num|	int	|点赞数|
|posts.collected|	int	|是否收藏(0未收藏，1已收藏)|
|posts.collected_num|	int	|收藏数|
|posts.replied|	int	|是否回复(0未回复，1已回复)|
|posts.replied_num|	int	|回复数|
|posts.image|array|帖子图片预览3张url地址|
|users.user_name|	string	|发帖人|
|users.profile_picture|string|用户头像图片url|
|users.user_id|int|发帖者id|
|groups.group_id|	int	|星球ID|
|groups.g_name|	string|	星球名称|


## 示例

显示第1页帖子

http://dev.wuanlife.com:800/post/get_index_post?user_id=1&pn=1

    JSON:
    {
    "ret": 200,
    "data": {
        "pageCount": 5,
        "currentPage": 1,
        "posts": [
            {
                "posts": {
                    "post_id": "42",
                    "p_title": "1",
                    "p_text": "1",
                    "lock": "0",
                    "create_time": "2017-03-16 11:09:13",
                    "approved": "1",
                    "approved_num": "3",
                    "collected": "0",
                    "collected_num": "0",
                    "replied": "0",
                    "replied_num": "0",
                    "image": []
                },
                "users": {
                    "profile_picture": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
                    "user_name": "汪汪汪",
                    "user_id": "2"
                },
                "groups": {
                    "group_id": "1",
                    "g_name": "装备2014中队"
                }
            },
            {
                "posts": {
                    "post_id": "41",
                    "p_title": "1",
                    "p_text": "1",
                    "lock": "0",
                    "create_time": "2017-03-13 22:06:15",
                    "approved": "0",
                    "approved_num": "0",
                    "collected": "0",
                    "collected_num": "0",
                    "replied": "0",
                    "replied_num": "0",
                    "image": []
                },
                "users": {
                    "profile_picture": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
                    "user_name": "汪汪汪",
                    "user_id": "2"
                },
                "groups": {
                    "group_id": "1",
                    "g_name": "装备2014中队"
                }
            }
        ]
    },
    "msg": null
    }
