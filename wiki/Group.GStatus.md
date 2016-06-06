#Group.GStatus

判断用户是否加入该星球

##接口调用请求说明

接口URL：http://apilost/?service=Group.GStatus

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--:|:--|
|user_id|整形|必须|-|用户ID|
|group_base_id|整型|必须|-|星球ID|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code|整型|操作码，1表示创建成功，0表示创建失败|
|msg                  |字符串 |提示信息|

##示例

判断用户是否加入该星球，1表示已加入，0表示未加入

http://apilost/?service=Group.GStatus&group_base_id=2

JSON:
    
    {    
        "ret": 200,    
        "data": {    
            "code": 1,    
            "msg": "已加入该星球！",    
            "info": []
        },
        "msg": ""

    }
