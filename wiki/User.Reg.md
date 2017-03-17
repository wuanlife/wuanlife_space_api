# user.reg

注册接口-用于验证并注册用户

## 接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/user/reg

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_name| 字符串 |   必须   |     最小：1    |         用户昵称|
|user_email   | 字符串|   必须  |        最小：1  |         用户邮箱|
|password| 字符串 |   必须   |      最小：1|           用户密码|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|msg        |   字符串 |提示信息|
|data.code         | 整型 |  操作码，1表示登录成功，0表示登录失败|
|info         | 对象 | 用户信息对象|
|info.user_id  | 整型  | 用户ID|
|info.user_name| 字符串| 用户昵称|
|info.user_email   | 字符串| 用户邮箱|

## 示例

注册账号(以下链接将提示该邮箱已注册！)

http://localhost/wuanlife_api/index.php/user/reg?user_name=dfgsdsd&user_email=1112&password=1

    JSON:
    {
        "ret": 200,
        "data": {
            "code": 1,
            "info": {
                "user_id": "192",
                "user_name": "dfgsdsd",
                "user_email": "1112"
            }
        },
        "msg": "注册成功，并自动登录！"
    }