#User.SendMail

邮件发送接口-用于发送邮件找回密码

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.SendMail

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|Email    |   字符串| 必须     ||           最小：1  |  用户邮箱|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|msg           |  字符串 |提示信息|
|code            |整型 |  操作码，1表示发送成功，0表示发送失败|


##示例

发送验证码到邮箱

http://dev.wuanlife.com:800/?service=User.Login&Email=taotao@taotao.com&password=111111
   
    JSON:
    {
    "ret": 200,
    "data": {
        "msg": "登录成功！",
        "code": "1",
        "info": {
            "userID": "26",
            "nickname": "taotao",
            "Email": "taotao@taotao.com"
        }
    },
    "msg": ""
    }
