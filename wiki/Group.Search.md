#Group.Search

搜索接口

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.Search&text=1

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|text|string|必须|搜索内容|


##返回说明
|参数|类型|说明|
|:--|:--|:--|
|group                |整型  |星球列表对象|
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

修改用户ID为1星球ID为1的星球信息

http://apihost/?service=Group.Search&text=5
     JSON:
{
    "ret": 200,
    "data": {
        "group": [
            {
                "name": "测试12580",
                "id": "12",
                "g_image": "../upload/group/2016/05/31/180854file.jpg",
                "g_introduction": "上传星球图片测试",
                "num": "1"
            },
            {
                "name": "测试5678",
                "id": "132",
                "g_image": null,
                "g_introduction": "5656565",
                "num": "1"
            },
            {
                "name": "鬼扯5",
                "id": "53",
                "g_image": null,
                "g_introduction": "null",
                "num": "1"
            }
        ],
        "posts": [
            {
                "postID": "12",
                "title": "biaoti15",
                "text": "77",
                "lock": "0",
                "createTime": "",
                "nickname": "azusa",
                "groupID": "15",
                "groupName": "sdfddd"
            },
            {
                "postID": "218",
                "title": "555555555555",
                "text": "<p>555555555555</p>",
                "lock": "0",
                "createTime": "2016-07-13 17:50:05",
                "nickname": "123",
                "groupID": "27",
                "groupName": "测试27"
            },
            {
                "postID": "182",
                "title": "123456",
                "text": "巴黎利库路特我某",
                "lock": "0",
                "createTime": "2016-06-21 11:53:52",
                "nickname": "汪",
                "groupID": "148",
                "groupName": "汪"
            },
            {
                "postID": "233",
                "title": "20160725 总结",
                "text": "你猜马来西亚参加奥运会至今共获得4银3铜7枚奖牌，其中5枚来自羽毛球项目，两枚来自跳水。毫无疑问，这次的夺金任务再次落在了目前羽毛球男子单打排名世界第一的李宗伟的身上，他将面临来自中国羽毛球名将林丹和谌龙的强力阻击。",
                "lock": "0",
                "createTime": "2016-07-15 21:10:26",
                "nickname": "火蚤族叶寻",
                "groupID": "166",
                "groupName": "叶氏春秋"
            }
        ]
    },
    "msg": ""
}
