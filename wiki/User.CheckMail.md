#User.CheckMail

邮箱验证接口-用于验证邮箱

##接口调用请求说明

接口URL：httpdev.wuanlife.com800service=User.CheckMail

请求方式：POST

参数说明：

参数名字    类型  是否必须    默认值    范围        说明
------------
Email       字符串 必须                最小：1    用户邮箱
code        字符串 可选                最小：1   验证码


##返回说明
参数类型说明
------
msg             字符串 提示信息
code            整型   操作码，1表示验证成功，0表示验证失败

##示例

发送验证码到邮箱

httpdev.wuanlife.com800service=User.CheckMail&Email=1195417752@qq.com&code=05629
    
    JSON
    {
    ret 200,
    data {
        code 1,
        msg 您的邮箱验证成功！
    },
    msg 
    }
