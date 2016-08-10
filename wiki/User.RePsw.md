#User.RePsw

重置密码接口-用于检验验证码的正确性并找回密码

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.RePsw

请求方式：POST

参数说明

|参数    |类型  |是否必须    |默认值    |范围        |说明|
|:--|:--|:--|:--|:--|:--|
|Email      | 字符串 |必须 |     |          最小：1   | 用户邮箱
|code        |字符串| 必须         ||       最小：1  |  验证码|
|password   | 字符串| 必须        ||        最小：1 |   用户密码|
|psw         |字符串| 必须     |     |      最小：1  |  用户二次确认密码|

##返回说明
|参数        |类型 |  说明|
|:--|:--|:--|
|msg            | 字符串| 提示信息|
|code          |  整型   |操作码，1表示重置成功，0表示重置失败|


##示例

重置密码

http://dev.wuanlife.com:800/?service=User.RePsw&Email=1195417752@qq.com&code=20759&password=666666&psw=666666
    
    JSON:
    {
    "ret": 200,
    "data": {
        "msg": "验证码不正确，请确认！",
        "code": 0
    },
    "msg": ""
    }
