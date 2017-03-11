#group.alter_group_info

修改星球详情

##接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/group/alter_group_info/group_id/user_id/g_introduction/g_image

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
|code	|	int	|1代表修改成功,0代表修改失败|
|msg	|	string	|报错信息|


##示例

修改用户ID为1星球ID为4的星球信息

http://localhost/wuanlife_api/index.php/group/alter_group_info/4/1

     JSON:
    {
        "ret": 200,
        "data": {
            "code": 1
        },
        "msg": "修改成功"
    }
