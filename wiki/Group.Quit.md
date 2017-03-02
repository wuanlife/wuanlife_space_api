#group.quit

退出星球接口-用户退出星球

##接口调用请求说明

接口URL：http://apihost/?service=Group.Quit

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_id|整形|必须|-|用户ID|
|group_id|整形|必须|最小：1 |星球ID|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code|整型|操作码，1表示退出成功，0表示退出失败|
|msg                  |字符串 |提示信息|

##示例

加入星球id为11的星球

http://apihost/?service=Group.Join&group_id=11


    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "msg": "退出成功！并通知星球创建者",
    },
    "msg": ""
	}
