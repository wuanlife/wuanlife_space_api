#user.alter_user_info

修改用户的信息

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/user/alter_user_info

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
|data |   int |1代表成功修改，0代表修改失败|
|msg|string|提示信息|

##示例

修改用户ID为1的信息

http://localhost/wuanlife_api/index.php/user/alter_user_info?user_id=1&user_name=123

     JSON:
    {
        "ret": 200,
        "data": 0,
        "msg": "用户名被占用，其他资料修改成功！"
    }