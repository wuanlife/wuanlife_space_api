#user.delete_message

删除信息接口-用于删除回复我的消息类型中帖子回复已被删除的消息

##接口调用请求说明

接口URL：http://localhost:88/index.php/user/delete_message

请求方式：GET

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|m_id|整型|必须|||消息ID|

##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示删除成功，0表示删除失败|
|msg |字符串 |提示信息|

##示例

删除用户m_id=19的消息

http://localhost:88/index.php/user/delete_message?m_id=19

    JSON:
    {
	"ret": 200,
	"data": {
		"code": 1
	},
	"msg": "删除成功"
	}