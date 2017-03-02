#group.user_manage

星球用户管理接口-用于显示加入星球的用户，方便管理

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.UserManage

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id|整型|必须|||用户ID|
|group_id|整型|必须|||星球ID|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，0表示没有权限或者成员数为零|
|info   | 数组  |用户信息详情|
|info.user_id | 整型| 用户ID|
|info.user_name | 字符串| 用户昵称|
|info.user_image|字符串|用户默认头像，都一样的|
|msg |字符串 |提示信息|


##示例

显示星球id=1的用户列表

http://dev.wuanlife.com:800/?service=Group.UserManage&group_id=1&user_id=2

    JSON：
    {
    "ret": 200,
    "data": {
        "info": [
            {
                "user_id": "3",
                "user_name": "6666"
            },
            {
                "user_id": "9",
                "user_name": "123"
            },
            {
                "user_id": "10",
                "user_name": "xiaochao_php"
            },
            {
                "user_id": "11",
                "user_name": "225"
            }
        ],
        "code": 1,
        "msg": "显示成功！"
    },
    "msg": ""
    }