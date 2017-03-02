#post.edit_post

帖子的编辑-单个帖子的编辑操作

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Post.editPost

请求方式：POST

参数说明：

|参数    |类型  |是否必须    |默认值    |范围             |说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |整型    |必须 ||                               |用户ID|
|post_id    | 整型   | 必须                                ||帖子ID|
|p_title       |字符串 | 必须        |        最小：1   |     |帖子标题|
|p_text       | 字符串 | 必须  |              最小：1   ||     帖子内容|

##返回说明
|字段    |        类型   |      说明|
|:--|:--|:--|
|code    |            整型      |  操作码，1表示编辑成功，0表示编辑失败|
|info.post_id |  整型      |  帖子ID|
|info.user_id   |整型     |   编辑人ID|
|info.p_title         | 字符串   |   编辑帖子标题|
|info.p_text           |字符串    |  编辑帖子内容|
|info.p_floor       |   整型      |  楼主楼层1|
|info.create_time    | 日期   |     编辑时间|

##示例

回复帖子

http://dev.wuanlife.com:800/?service=post.editPost&post_id=1&text=666999&user_id=1&title=666666

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": {
            "post_base_id": 1,
            "user_base_id": 1,
            "title": "666666",
            "text": "666999",
            "floor": 1,
            "createTime": "2016-04-22 20:05:07"
        }
    },
    "msg": ""
    }
