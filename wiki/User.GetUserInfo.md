#User.GetUserInfo

获取用户详情

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.getUserInfo&user_id=1

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|user_id|int|必须|用户ID|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|userID|int|用户id|
|Email|string|用户Email|
|nickname|string|用户名称|
|sex|int|	用户性别,0为未设，1为男，2为女|
|year|string|年|
|month|string|月|
|day|string|	日|
|mailChecked|	string|	是否验证邮箱，0为未验证邮箱，1为已验证邮箱|


##示例

获取用户ID为1的用户信息

http://dev.wuanlife.com:800/?service=User.getUserInfo&user_id=1

JSON
{
    "ret": 200,
    "data": {
        "userID": "1",
        "sex": "0",
        "year": "1000",
        "month": null,
        "day": null,
        "mailChecked": "0",
        "Email": null,
        "nickname": null
    },
    "msg": ""
}

