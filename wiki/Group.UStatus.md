# group.u_status

判断用户登陆状态-判断是否登录

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/group/check_status

请求方式：GET

参数说明：

|参数名字|类型|是否必须|范围|说明|
|:--|:--|:--|:--:|:--|
|user_id|整形|必须|-|用户ID|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|code           | 整型 | 操作码，1表示已登录，0表示未登录|
|info           | 对象 | 状态信息对象|
|info.user_id       |  整型 | 用户ID|
|info.user_name |  字符串| 用户昵称|
|msg            | 字符串| 提示信息|

## 示例

判断用户登陆状态，1表示已登录，0表示未登录

http://dev.wuanlife.com:800/group/check_status?user_id=85

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": {
            "user_id": "85",
            "user_name": "lwy_test"
        }
    },
    "msg": "用户已登陆"
    }
