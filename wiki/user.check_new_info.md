#user.check_new_info

用户消息未读检查接口-检查用户是否有新信息

##接口调用请求说明

接口URL：http://localhost:88/index.php/user/check_new_info

请求方式：GET

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     ||           最小：1  |  用户ID|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|num|整型|1有信息 0没有
|msg |null |null|


##示例

显示用户id=1的消息列表

http://localhost:88/index.php/user/check_new_info?user_id=58

    JSON：
    {
	"ret": 200,
	"data": {
		"num": 1
	},
	"msg": null
    }