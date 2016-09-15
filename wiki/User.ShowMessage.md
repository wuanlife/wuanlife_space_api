#User.ShowMessage

用户消息中心接口-用于接收其他用户发送给用户消息

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.ShowMessage

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     ||           最小：1  |  用户ID|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情|
|info.information | 字符串| 用户消息详情|
|info.createTime |字符串 |创建时间|
|info.read  | 整型  |是否已读|
|msg |字符串 |提示信息|


##示例

发送验证码到邮箱

http://dev.wuanlife.com:800/?service=User.ShowMessage&user_id=1

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": [
            {
                "information": "69694469897申请加入26name星球。",
                "createTime": "1970-01-01 08:00",
                "read": "0"
            },
            {
                "information": "azusa同意你加入好无聊9999q星球。",
                "createTime": "2031-11-16 10:07",
                "read": "0"
            },
            {
                "information": "你申请加入测试星球已被奇奇拒绝。",
                "createTime": "1970-01-01 08:00",
                "read": "0"
            }
        ],
        "msg": "接收成功"
    },
    "msg": ""
    }
