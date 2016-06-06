#Group.Join

加入星球接口-用户加入星球

##接口调用请求说明

接口URL：http://apihost/?service=Group.Join

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_id|整形|必须|-|用户ID|
|group_base_id|整形|必须|最小：1 |星球ID|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code|整型|操作码，1表示创建成功，0表示创建失败|
|info                 |对象   |星球信息对象|
|info.group_base_id   |整型   |加入星球ID|
|info.user_base_id    |字符串 |加入者ID|
|info.authoriza   |字符串 |身份|
|msg                  |字符串 |提示信息|

##示例

加入星球id为11的星球

http://apihost/?service=Group.Join&group_base_id=11


    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "msg": "",
        "info": {
            "group_base_id": 11,
            "user_base_id": "15"
            }
        },
    "msg": ""
    }
