# user.get_user_info

获取用户详情

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/user/get_user_info

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|user_id|int|必须|用户ID|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|user_id|int|用户id|
|user_email|string|用户Email|
|user_name|string|用户呢称|
|sex|int|	用户性别,0为未设，1为男，2为女|
|year|string|年|
|month|string|月|
|day|string|	日|
|mail_checked|	string|	是否验证邮箱，0为未验证邮箱，1为已验证邮箱|
|profile_picture|string|用户头像|

## 示例

获取用户ID为1的用户信息

http://dev.wuanlife.com:800/user/get_user_info?user_id=1

    JSON
    {
        "ret": 200,
        "data": {
            "userID": "1",
            "sex": "0",
            "year": "",
            "month": "",
            "day": "",
            "mail_checked": "1",
            "profile_picture": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
            "email": "taotao@taotao.com",
            "nickname": "我是一只鸟"
        },
        "msg": null
    }

