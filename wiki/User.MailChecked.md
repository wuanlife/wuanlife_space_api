# user.check_mail_2

邮箱验证接口-用于检验验证码的正确性并验证邮箱

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/user/check_mail_2

请求方式：GET

参数说明：

|参数名字  |  类型  |是否必须|    默认值    |范围  |      说明|
|:--|:--|:--|:--|:--|:--|
|user_id   |   字符串| 必须     |        |   最小：1 |   用户id，由token解密|


## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|msg       |      字符串 |提示信息|
|code       |     整型|   操作码，1表示验证成功，0表示验证失败|

## 示例

发送验证码到邮箱

http://dev.wuanlife.com:800/user/check_mail_2?user_id=1

    JSON
    {
    "ret": 200,
    "data": {
        "code": 1
    },
    "msg": "验证成功"
    }
