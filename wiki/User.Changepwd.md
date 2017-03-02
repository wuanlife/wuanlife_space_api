#user.change_pwd

修改密码接口-用于验证旧密码，修改新密码

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.Changepwd

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     ||           最小：1  |  用户ID|
|pwd|字符串|必须| |最小：6|登录密码|
|new_pwd|字符串|必须| |最小：6|新密码|
|check_new_pwd|字符串|必须| |最小：6|二次确认新密码|

##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示修改成功，0表示修改失败|
|msg |字符串 |提示信息|


##示例

修改用户id=1的密码

http://dev.wuanlife.com:800/?service=User.Changepwd&user_id=1&pwd=1&newpwd=1&checkNewpwd=1

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 0,
        "msg": "登录密码不正确"
    },
    "msg": ""
    }
