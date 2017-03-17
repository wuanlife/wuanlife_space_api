# user.alter_read **存疑**

已读接口-用于将未读消息标记为已读

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.AlterRead

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     ||           最小：1  |  用户ID|
|message_code|  整型  |必须     || 最小：1|    消息类型，0001、0002、0003分别代表申请，同意，拒绝|
|countnum |   整型|  必须    ||      计数参数，用于区分同一类型的消息|


## 返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示操作成功，0表示操作失败|
|msg |字符串 |提示信息|


## 示例

发送验证码到邮箱

http://dev.wuanlife.com:800/?service=User.AlterRead&message_code=0001&user_id=1&countnum=1

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 0,
        "msg": "操作失败！"
    },
    "msg": ""
    }
