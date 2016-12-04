#User.DeleteMessage

删除信息接口-用于删除回复我的消息类型中帖子回复已被删除的消息

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.DeleteMessage

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|m_id|整型|必须|||消息ID|

##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示删除成功，0表示删除失败|
|msg |字符串 |提示信息|

##示例

显示用户m_id=3的消息

http://dev.wuanlife.com:800/?service=User.DeleteMessage&m_id=3

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 0,
        "msg": "删除失败"
    },
    "msg": ""
    }