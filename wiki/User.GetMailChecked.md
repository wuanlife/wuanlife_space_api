# user.get_mail_checked

确认邮箱验证接口-用于验证用户邮箱是否已被验证

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.GetMailChecked

请求方式：POST

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|user_id|整型|必须|用户id|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|user_id|整型|用户ID|
|mail_checked|字符串|是否验证邮箱，0为未验证邮箱，1为已验证邮箱|


## 示例

确认id=1用户是否已经验证邮箱（是）

http://dev.wuanlife.com:800/?service=User.GetMailChecked&user_id=1

    JSON:
    {
    "ret": 200,
    "data": {
        "userID": "1",
        "mailChecked": "1"
    },
    "msg": ""
    }

