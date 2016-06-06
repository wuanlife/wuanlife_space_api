#Group.GetJoined

获取用户加入星球的接口

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.GetJoined

请求方式：GET

参数说明：

|参数名字       | 类型 | 是否必须  |  默认值  |  范围   |                说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |     整型 |  必须  |       |                              | 用户id|
|page       |整型   |可选          | 1   |                              |当前页面|

##返回说明

|返回字段                 |类型    |    说明|
|:--|:--|:--|
|groups.name   |           string     | 星球名称|
|groups.id          |      int  |       星球id|
|groups.g_image   |        string    |  星球图片|
|groups.g_introduction  |  string   |   星球介绍|
|group.num          |      int      |   星球数量|
|pageCount        |        int   |      总页数|
|currentPage    |          int   |      当前页|

##示例

显示用户ID为1所加入的星球

http://dev.wuanlife.com:800/?service=Group.GetJoined&user_id=1

    JSON
    {
    "ret": 200,
    "data": {
        "groups": [
            {
                "name": "666999",
                "id": "11",
                "g_image": null,
                "g_introduction": null,
                "num": "2"
            },
            {
                "name": "午安网",
                "id": "2",
                "g_image": null,
                "g_introduction": null,
                "num": "2"
            }
        ],
        "pageCount": 1,
        "currentPage": 1
    },
    "msg": ""
    }
