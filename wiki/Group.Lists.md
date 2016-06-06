#Group.Lists

星球列表-按成员数降序显示星球列表

##接口调用请求说明

接口URL：http://apilost/?service=Group.Lists

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--:|:--|
|page|整型|可选|-|当前页面|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|lists                |整型  |星球列表对象|
|lists.name           |整型   |星球名称|
|lists.g_image           |字符型   |星球图片|
|lists.g_introduction           |字符型   |星球介绍|
|lists.id     |整型 |星球ID|
|lists.num           |整型 |星球成员数|
|pageCount           |整型 |总页数|
|currentPage           |整型 |当前页|


##示例

显示第1页星球列表

http://apilost/?service=Group.Lists&page=1

    JSON:
        {
        "ret": 200,
        "data": {
        "lists": [
            {
                "name": "asdasf",
                "id": "4",
                "num": "3"
            },
            
            {
                "name": "123",
                "id": "31",
                "num": "1"
            },

            {
                "name": "weref",
                "id": "32",
                "num": "1"
            },
            {
                "name": "qwefdsf112",
                "id": "25",
                "num": "1"
            }
        ]
    },
    "msg": ""
    }
