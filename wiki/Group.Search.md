#Group.Search

搜索接口

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.Search&text=1

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|text|string|必须|搜索内容|
|pn|int|可选|帖子页数|
|gn|int|可选|星球页数|


##返回说明
|参数|类型|说明|
|:--|:--|:--|
|group.name           |字符型   |星球名称|
|group.g_image           |字符型   |星球图片|
|group.g_introduction           |字符型   |星球介绍|
|group.id     |整型 |星球ID|
|group.num           |整型 |星球成员数|
|posts.postID   |   int|    帖子ID|
|posts.title|   string| 标题|
|posts.text |string |内容|
|posts.createTime|  date|   发帖时间|
|posts.nickname|    string  |发帖人|
|posts.groupID| int |星球ID|
|posts.lock|    int |是否锁定|
|posts.groupName|   string| 星球名称|



##示例

搜索text为8的相关内容

http://apihost/?service=Group.Search&text=8
     JSON:
{
    "ret": 200,
    "data": {
        "group": [
            {
                "name": "cs48",
                "id": "48",
                "g_image": null,
                "g_introduction": "cs48",
                "num": "2"
            },
            {
                "name": "789",
                "id": "20",
                "g_image": null,
                "g_introduction": "测试20",
                "num": "2"
            },
            {
                "name": "789789",
                "id": "60",
                "g_image": "/home/www/html/wuanlife_api/Demo/Domain/../upload/group/2016/06/01/125809file.jpg",
                "g_introduction": "上传测试",
                "num": "2"
            }
        ],
        "posts": [
            {
                "postID": "2",
                "title": "午安煎饼计划Android组第48周周报",
                "text": "<p>hello，我是午安熊，大家上午好！</p>",
                "lock": "0",
                "createTime": "2016-05-20 20:02:51",
                "nickname": "午安网",
                "groupID": "2",
                "groupName": "午安网啊阿萨阿萨安师大"
            }
        ]
    },
    "msg": ""
}
