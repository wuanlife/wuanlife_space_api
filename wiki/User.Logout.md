#user.logout

注销接口-用于清除用户登录信息

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/user/logout

请求方式：GET

参数说明：

无

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|msg   |          字符串 |提示信息|
|code     |       整型 |  操作码，1表示注销成功，0表示注销失败|

##示例

注销账号(均返回注销成功)

http://localhost/wuanlife_api/index.php/user/logout

    JSON:
    {
        "ret": 200,
        "data": {
            "code": "1"
        },
        "msg": "注销成功！"
    }
