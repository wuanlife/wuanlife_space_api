# group.get_group_info

获取星球详情

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/group/get_group_info

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|group_id|int|必须|星球id|
|user_id|int|必须|用户id|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|group_id|int|	星球id|
|g_name|string|星球名称|
|g_introduction|string|	星球介绍|
|g_image|string|	星球图片链接|
|private|int|是否私密，0允许任何人加入，1需申请方可加入|
|creator|int|是否为创建者，1为创建者，0不是创建者|


## 示例

获取用户ID为2星球ID为1的星球信息

http://dev.wuanlife.com:800/group/get_group_info?group_id=1&user_id=2

    JSON
    {
    "ret": 200,
    "data": {
        "group_id": "1",
        "g_name": "装备2014中队",
        "g_introduction": null,
        "g_image": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
        "private": "0",
        "creator": 1
    },
    "msg": null
    }
