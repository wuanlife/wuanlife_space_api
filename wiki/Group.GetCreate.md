#group.get_create

获取用户创建星球的接口

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.GetCreate

请求方式：GET

参数说明：

|参数名字        |类型  |是否必须    |默认值    |范围                   |说明|
|:--|:--|:--|:--|:--|:--|
|user_id        |整型   |必须          ||                              |用户id|
|page       |整型   |可选          | 1   |                              |当前页面|

##返回说明

|返回字段                | 类型   |     说明|
|:--|:--|:--|
|groups.g_name       |       string    |  星球名称|
|groups.group_id           |     int   |      星球id|
|groups.g_image     |      string   |   星球图片|
|groups.g_introduction |   string   |   星球介绍|
|group.num         |       int     |    星球数量|
|page_count           |     int     |    总页数|
|current_page        |      int   |      当前页|

##示例

显示用户ID为1所加入的星球

http://dev.wuanlife.com:800/?service=Group.GetCreate&user_id=1

    JSON
    {
    "ret": 200,
    "data": {
        "groups": [
            {
                "g_name": "测试测试789",
                "group_id": "4",
                "g_image": "../upload/group/2016/05/30/195108file.jpg",
                "g_introduction": "测试",
                "num": "1"
            }
        ],
        "pageCount": 1,
        "currentPage": 1
    },
    "msg": ""
    }
