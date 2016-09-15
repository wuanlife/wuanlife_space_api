#Group.PrivateGroup

私密星球申请加入接口-用于申请者加入私密星球

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Group.PrivateGroup

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|Email    |   字符串| 必须     ||           最小：1  |  用户邮箱|
|user_id    |整型 |必须 ||   最小：1   |用户ID|
|group_id   |整型|    必须  ||   最小：1   |星球ID|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|msg           |  字符串 |提示信息|
|code            |整型 |  操作码，1表示申请成功，0表示申请失败|


##示例

发送验证码到邮箱

http://dev.wuanlife.com:800/?service=Group.PrivateGroup&user_id=5&group_id=22

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "msg": "申请成功！请等待创建者审核！"
    },
    "msg": ""
    }
