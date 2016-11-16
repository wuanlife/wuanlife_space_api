#User.AlterUserInfo

修改用户的信息

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.alterUserInfo&user_id=1&sex=F&year=2000&month=1&day=4

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|user_id|int|必须|用户ID|
|user_name|string|可选|用户昵称|
|profile_picture|string|可选|用户头像|
|sex|int|可选|性别|
|year|string|可选|年|
|month|string|可选|月|
|day|string|可选|日|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code_1	|	int	|1代表其他资料成功修改，0代表其他资料修改失败|
|code_2|int|1代表成功修改用户昵称，0代表未修改|
|msg_1|string|其他资料修改情况|
|msg_2|string|用户昵称修改情况|

##示例

修改用户ID为1的信息

http://dev.wuanlife.com:800/?service=User.alterUserInfo&user_id=1&sex=F&year=2000&month=1&day=4

     JSON:
    {
    "ret": 200,
    "data": {
        "data": 1,
        "msg": "修改成功"
    },
    "msg": ""
     }
