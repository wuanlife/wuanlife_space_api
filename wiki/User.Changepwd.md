# user.change_pwd

修改密码接口-用于验证旧密码，修改新密码

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/user/change_pwd

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     ||           最小：1  |  用户ID|
|password|字符串|必须| |最小：6|登录密码|
|psw|字符串|必须| |最小：6|新密码|
|check_psw|字符串|必须| |最小：6|二次确认新密码|

## 返回说明

|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示修改成功，0表示修改失败|
|msg |字符串 |提示信息|


## 示例

修改用户id=85的密码

http://dev.wuanlife.com:800/user/change_pwd

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 1
    },
    "msg": "修改成功"
    }
