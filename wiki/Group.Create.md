#group.create

星球创建接口-用于创建星球

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/group/create/user_id/g_name/g_image/g_introduction/private

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_id|整型|必须|-|用户id|
|g_name|字符串|必须|最小：1 最大：80|星球名称|
|g_image|字符串  | 可选 ||  星球图片base64编码|
|g_introduction|字符串|可选||星球简介|
|private|整型|可选||私密，1为私密0为不私密|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code|整型|操作码，1表示创建成功，0表示创建失败|
|info                 |对象   |星球信息对象|
|info.group_id   |整型   |星球ID|
|info.user_id    |字符串 |创建者ID|
|info.g_name            |字符串 |星球名称|
|info.g_introduction   |字符串  | 星球简介|
|info.g_image        |字符串|星球图片路径|
|msg                  |字符串 |提示信息|
|info.authorization   |字符串 |权限，01表示创建者，02表示管理员，03表示会员|

##示例

创建名为“dk6689”的星球

http://localhost/wuanlife_api/index.php/group/create/1/dk6689

    JSON:
    {
        "ret": 200,
        "data": {
            "code": 1,
            "info": {
                "g_name": "dk6689",
                "group_id": "409",
                "g_image": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
                "g_introduction": null,
                "user_id": "1",
                "authorization": "01"
            }
        },
        "msg": "创建成功！"
    }
