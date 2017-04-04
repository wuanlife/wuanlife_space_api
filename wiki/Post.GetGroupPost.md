# post.get_group_post

星球页面帖子显示

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/post/get_group_post

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--:|:--|
|group_id|int|必须|-|星球ID|
|user_id|int|可选|-|用户ID|
|pn|int|可选|1|第几页|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|creator_id	|int|	星球创建者id|
|creator_name  |string|   星球创建者名称|
|group_id|int	|星球ID|
|g_image|string|星球图片url|
|g_introduction|string|星球简介|
|g_name	|string|	星球名称|
|identity    |int    |01创建者 02成员 03非成员未申请 04非成员申请中|
|private    |int    |0否 1私密|
|post_num|int|话题数|
|user_num|int|成员数|
|-|-|-|
|posts.digest	|	int|	是否加精 1为加 0为不加|
|posts.sticky	|string|	是否置顶 1为置顶 0为不置顶|
|posts.lock	|int|	是否锁定 1为锁定 0为不锁定|
|posts.post_id	|	int|	帖子ID|
|posts.p_title|string|帖子标题|
|posts.p_text	|string|帖子内容|
|posts.create_time|	date|	发帖时间|
|posts.approved|	int	|是否点赞(0未点赞，1已点赞)|
|posts.approved_num|	int	|点赞数|
|posts.collected|	int	|是否收藏(0未收藏，1已收藏)|
|posts.collected_num|	int	|收藏数|
|posts.replied|	int	|是否回复(0未回复，1已回复)|
|posts.replied_num|	int	|回复数|
|-|-|-|
|users.user_id|int|发帖人id|
|users.user_name|string	|发帖人昵称|
|users.profile_picture|string|发帖人头像url|
|-|-|-|
|page_count	|int	|总页数|
|current_page	|int	|当前页|


## 示例

显示星球ID为16的帖子

http://dev.wuanlife.com:800/post/get_group_post?group_id=1&pn=1

    JSON:
    {
    "ret": 200,
    "data": {
        "group_id": "1",
        "g_name": "装备2014中队",
        "g_introduction": null,
        "g_image": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
        "private": "0",
        "creator_id": "2",
        "creator_name": "汪汪汪",
        "post_num": 6,
        "user_num": 17,
        "identity": "03",
        "posts": [
            {
                "posts": {
                    "post_id": "42",
                    "p_title": "1",
                    "p_text": "1",
                    "lock": "0",
                    "digest": "0",
                    "sticky": "0",
                    "create_time": "2017-03-16 11:09:13",
                    "approved": "0",
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
                }
            },
            {
                "posts": {
                    "post_id": "41",
                    "p_title": "1",
                    "p_text": "1",
                    "lock": "0",
                    "digest": "0",
                    "sticky": "0",
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
                }
            }
        ],
        "page_count": 1,
        "current_page": "1"
    },
    "msg": "浏览帖子成功"
    }
