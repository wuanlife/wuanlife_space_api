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
|num|字符型|未读消息数|
|info.information | 字符串| 用户消息详情|
|info.createTime |字符串 |创建时间|
|info.status |字符串 |0未读 1已读 2已同意 3已拒绝|
|info.messagetype  | 字符串  |0001：申请消息 0002：同意消息 0003：拒绝消息|
|msg |字符串 |提示信息|


##示例

显示用户id=1的消息列表

http://dev.wuanlife.com:800/?service=User.ShowMessage&user_id=1

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": [
            {
                "information": "ccc申请加入装备2014中队星球。",
                "createTime": "2016-09-18 08:42",
                "status": "1",
                "messagetype": "0001"
            }
        ],
        "msg": "接收成功"
    },
    "msg": ""
    }
