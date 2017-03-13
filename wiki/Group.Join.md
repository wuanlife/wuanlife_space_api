#group.join

加入星球接口-用户加入星球

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/group/join/user_id/group_id

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_id|整形|必须|-|用户ID|
|group_id|整形|必须|最小：1 |星球ID|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|data|boolen|操作码，true表示加入成功，false表示加入失败|
|msg                  |字符串 |提示信息|

##示例

加入星球id为4的星球

http://localhost/wuanlife_api/index.php/group/join?user_id=17&group_id=11


    JSON:
    {
        "ret": 200,
        "data": true,
        "msg": "加入成功！并通知星球创建者"
    }
