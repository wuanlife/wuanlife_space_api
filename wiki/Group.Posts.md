#Group.Posts

帖子发布-星球帖子发布

##接口调用请求说明

接口URL：http://apihost/?service=Group.Posts

请求方式： POST

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_id|整形|必须|-|用户ID|
|group_base_id  | 整型  | 必须   |               最小：1  |  发帖星球|
|title          | 字符串 |必须   |               最小：1  |   帖子标题|
|text           | 字符串| 必须  |                最小：1 |  帖子正文|
|p_image|字符串|可选||帖子图片base64编码|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code               | 整型 | 操作码，1表示发布成功，0表示发布失败|
|info               | 对象 | 帖子信息对象|
|info.group_base_id | 整型 | 帖子所属星球ID|
|info.post_base_id | 整型 | 帖子ID|
|info.text         |  字符串 |帖子正文|
|info.floor        |  整型  |帖子楼层|
|info.createTime    | 字符串 |帖子发布时间|
|info.title         | 字符串| 帖子标题|
|info.URL | 字符串  | 帖子图片地址|
|msg               |  字符串 |提示信息|

##示例

在id为3的星球创建标题为123，内容为qweasf的帖子

http://apihost/?service=Group.Posts

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "msg": "",
        "info": {
            "post_base_id": "34",
            "user_base_id": "15",
            "text": "qweasf",
            "floor": "1",
            "createTime": "2016-04-09 20:08:19",
            "title": "123"，
            "URL": "../upload/posts/2016/05/29/204338file.jpg"
        }
    },
    "msg": ""
    }
