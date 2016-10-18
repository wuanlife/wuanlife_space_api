#Group.Search

搜索接口

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.Search&text=1

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|text|string|必须|搜索内容|
|pnum|int|可选|帖子每页数量|
|pn|int|可选|帖子页数|
|gnum|int|可选|星球每页数量|
|gn|int|可选|星球页数|


##返回说明
|参数|类型|说明|
|:--|:--|:--|
|group.name           |字符型   |星球名称|
|group.g_image           |字符型   |星球图片|
|group.g_introduction           |字符型   |星球介绍|
|group.id     |整型 |星球ID|
|group.num           |整型 |星球成员数|
|groupPage          |整型 |星球总页数|
|posts.postID   |   int|    帖子ID|
|posts.title|   string| 标题|
|posts.text |string |内容|
|posts.createTime|  date|   发帖时间|
|posts.nickname|    string  |发帖人|
|posts.groupID| int |星球ID|
|posts.lock|    int |是否锁定|
|posts.groupName|   string| 星球名称|
|groupPage          |整型 |帖子总页数数|



##示例

修改用户ID为1星球ID为1的星球信息

http://apihost/?service=Group.Search&text=1&pnum=1&pn=31&gnum=1&gn=60
    
	JSON:
    {
        "ret": 200,
        "data": {
            "group": [
                {
                    "name": "jinjin111",
                    "id": "242",
                    "g_image": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
                    "g_introduction": null,
                    "num": "1"
                }
            ],
            "GroupPage": 60,
            "posts": [
                {
                    "postID": "246",
                    "title": "100000",
                    "text": "皮下赛季",
                    "lock": "0",
                    "createTime": "2016-09-02 10:25:05",
                    "nickname": "12222",
                    "groupID": "1",
                    "groupName": "装备2014中队和是加"
                }
            ],
            "PostsPage": 31
        },
        "msg": ""
    }
