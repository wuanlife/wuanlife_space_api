#Group.AlterGroupInfo

修改星球详情

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.alterGroupInfo&group_id=1&user_id=1&g_introduction=1$g_image=1

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|group_id|int|必须|星球id|
|user_id|int|必须|用户ID|
|g_introduction|string|可选|星球简介|
|g_image|string|可选|星球图片|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|data	|	int	|1代表修改成功,0代表修改失败|
|msg	|	string	|报错信息|


##示例

修改用户ID为1星球ID为1的星球信息

http://apihost/?service=Group.alterGroupInfo&group_id=1&user_id=1&g_introduction=1$g_image=1

     JSON:
     {
    "ret": 200,
    "data": {
        "data": 1,
        "msg": "修改成功"
    },
    "msg": ""
    }
