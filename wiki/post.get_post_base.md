# post.get_post_base

帖子详情-发帖内容

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/post/get_post_base

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|post_id|int|必须|帖子ID|
|user_id|int|可选|用户ID|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|code|int|操作码，帖子删除为0. 正常显示为1. 私密帖子为2|
|msg|string|提示信息|
|groups|-|-|
|group_id	|	int	|星球ID|
|g_name|	string	|星球名称|
|g_image|string|星球图片|
|g_introduction|string|星球简介|
|creator_id|int|创建者id|
|creator_name|string|创建者昵称|
|posts|-|-|
|post_id	|	int	|帖子ID|
|p_title	|string|	标题|
|p_text	|string|	内容|
|create_time|	date|	发帖时间|
|sticky   |int|    是否置顶（0为未置顶，1置顶）|
|lock|    int|   是否锁定（0为未锁定，1锁定）|
|approved|	int	|是否点赞(0未点赞，1已点赞)|
|approved_num|	int	|点赞数|
|collected| int |   帖子是否收藏(0为未收藏，1为收藏)|
|collected_num|	int	|收藏数|
|p_image|string|帖子内容图片，用户安卓端|
|users|-|-|
|user_id|	int	|用户ID|
|user_name	|string|	发帖人|
|profile_picture|string|发帖人头像|
|rights|-|-|
|edit_right|	boolean	|	编辑权限(0为无权限，1有)|
|delete_right|	boolean	|	删除权限(0为无权限，1有)|
|sticky_right|	boolean	|	置顶权限(0为无权限，1有)|
|lock_right|	boolean	|	锁帖权限(0为无权限，1有)|



## 示例

显示帖子ID为1的帖子内容,此人为发帖者

http://dev.wuanlife.com:800/post/get_post_base?user_id=1&post_id=1

     JSON:
    {
    "ret": 200,
    "data": {
        "groups": {
            "group_id": "2",
            "g_name": "午安网",
            "g_image": "",
            "g_introduction": "",
            "creator_id": "3",
            "creator_name": "HQ6"
        },
        "posts": {
            "post_id": "1",
            "p_title": "通过接口编辑",
            "p_text": "成功",
            "create_time": "2017-02-12 20:26:06",
            "sticky": 0,
            "lock": 0,
            "approved": "0",
            "approved_num": "0",
            "collected": "0",
            "collected_num": "0",
            "p_image": [
                []
            ]
        },
        "users": {
            "user_id": "58",
            "user_name": "xiaochao_php",
            "profile_picture": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2/1/w/100/h/100"
        },
        "rights": {
            "edit_right": 1,
            "delete_right": 1,
            "sticky_right": 0,
            "lock_right": 1
        },
        "code": 1
    },
    "msg": "查看帖子成功"
    }
