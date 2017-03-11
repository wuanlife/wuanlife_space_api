#user.login

登录接口-用于验证并登录用户

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/user/login/email/password

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|email     |  字符串| 必须    |      最小：1|           用户邮箱|
|password  |  字符串 |必须     |       最小：1|         用户密码|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|msg        |     字符串 |提示信息|
|code       |     整型  | 操作码，1表示登录成功，0表示登录失败|
|info         |   对象 |  用户信息对象|
|info.user_id  |   整型  | 用户ID|
|info.user_name |  字符串 |用户昵称|
|info.user_email    |  字符串 |用户邮箱|

##示例

注册账号

http://localhost/wuanlife_api/index.php/user/login/ch1111ac/1

    JSON:
    {
        "ret": 200,
        "data": {
            "code": "1",
            "info": {
                "user_id": "189",
                "user_name": "c1ha11c11",
                "user_email": "ch1111ac"
            }
        },
        "msg": "登录成功！"
    }
