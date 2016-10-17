#Group.DeleteGroupMember

星球用户管理接口-用于显示加入星球的用户，方便管理

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.DeleteGroupMember

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id|整型|必须|||用户ID|
|group_id|整型|必须|||星球ID|
|member_id|整型|必须||成员ID|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示删除成功，0表示删除失败|
|msg |字符串 |提示信息|


##示例

显示用户id=1的消息列表

http://dev.wuanlife.com:800/?service=Group.DeleteGroupMember&group_id=1&user_id=2&member_id=1

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 0,
        "msg": "操作失败！"
    },
    "msg": ""
    }