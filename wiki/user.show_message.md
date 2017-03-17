# user.show_message 

用户消息中心接口-用于接收其他用户发送给用户消息

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/user/show_message

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     |-|           最小：1  |  用户ID|
|pn|整型|必须 |默认4|-|消息页码|
|m_type|整型|必须|默认1|-|消息分类，见下行|

## 消息分类，1帖子通知，2星球通知，3星球验证消息，4.消息主页面

## 返回说明——1

|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.users.user_id | 整型| 回复人ID|
|info.messages.m_id | 整型| 消息ID|
|info.posts.reply_floor|字符型|回复人楼层|
|info.users.user_name|字符型|回复人昵称|
|info.messages.image|字符串|回复人头像|
|info.posts.p_title|字符串|帖子标题|
|info.posts.post_id|整型|帖子id|
|info.posts.page|整型|回复所在的回复列表页码数|
|page_count|整型|总页码|
|current_page|整型|当前页码|
|msg |字符串 |提示信息|

## 示例——1

显示用户id=1的消息列表帖子通知

http://dev.wuanlife.com:800/user/show_message

    JSON:
    {
	"ret": 200,
	"data": {
		"code": 1,
		"info": [
			{
				"users": {
					"user_id": "58",
					"user_name": "xiaochao_php"
				},
				"posts": {
					"post_id": "5",
					"p_title": "test",
					"reply_floor": "14",
					"page": 1
				},
				"messages": {
					"m_id": "13",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2\/1\/w\/100\/h\/100"
				}
			},
			{
				"users": {
					"user_id": "58",
					"user_name": "xiaochao_php"
				},
				"posts": {
					"post_id": "5",
					"p_title": "test",
					"reply_floor": "13",
					"page": 1
				},
				"messages": {
					"m_id": "12",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2\/1\/w\/100\/h\/100"
				}
			}
		],
		"page_count": 1,
		"current_page": 1
	},
	"msg": "接收成功"
}

## 返回说明——2

|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.messages.m_id | 整型| 消息ID|
|info.users.user_id|整型|用户id|
|info.messages.type|整型|消息类型，详情见content参数，5种|
|info.users.user_name|字符型|用户昵称|
|info.messages.status|整型|0未读1已读|
|info.groups.g_name|字符型|星球名称|
|info.groups.group_id |整型|星球ID|
|info.content |字符串 |消息内容预览|
|info.groups.image|字符串|用户头像或者星球头像url|
|page_count|整型|总页码
|current_page|整型|当前页码
|msg |字符串 |提示信息|

## 示例——2

显示用户id=1的消息列表星球通知

http://dev.wuanlife.com:800/user/show_message

    JSON:
    {
	"ret": 200,
	"data": {
		"code": 1,
		"info": [
			{
				"content": "叶寻退出了sdfdddc",
				"users": {
					"user_id": "46",
					"user_name": "叶寻"
				},
				"groups": {
					"group_id": "16",
					"g_name": "sdfdddc"
				},
				"messages": {
					"m_id": "28",
					"type": "4",
					"status": "1",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
				}
			},
			{
				"content": "叶寻退出了测试27",
				"users": {
					"user_id": "46",
					"user_name": "叶寻"
				},
				"groups": {
					"group_id": "27",
					"g_name": "测试27"
				},
				"messages": {
					"m_id": "27",
					"type": "4",
					"status": "1",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
				}
			}
		],
		"page_count": 10,
		"current_page": 1
	},
	"msg": "接收成功"
    }

## 返回说明——3

|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.messages.m_id | 整型| 消息ID|
|info.users.user_id|整型|用户ID|
|info.messages.text|字符型|申请理由|
|info.messages.image|字符型|用户头像|
|info.users.user_name|字符型|用户昵称|
|info.groups.g_name|字符型|星球名称|
|info.messages.status|整型|消息是否处理1未处理2已同意3已拒绝|
|page_count|整型|总页码
|current_page|整型|当前页码
|msg |字符串 |提示信息|


## 示例——3

显示用户id=1的消息列表星球验证消息

http://dev.wuanlife.com:800/user/show_message

    JSON：
    {
	"ret": 200,
	"data": {
		"code": 1,
		"info": [
			{
				"users": {
					"user_id": "58",
					"user_name": "xiaochao_php"
				},
				"groups": {
					"group_id": "358",
					"g_name": "私密星球_666"
				},
				"messages": {
					"m_id": "22",
					"status": "1",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2\/1\/w\/100\/h\/100",
					"text": "24531"
				}
			},
			{
				"users": {
					"user_id": "58",
					"user_name": "xiaochao_php"
				},
				"groups": {
					"group_id": "358",
					"g_name": "私密星球_666"
				},
				"messages": {
					"m_id": "19",
					"status": "1",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2\/1\/w\/100\/h\/100",
					"text": "测试中  请稍后"
				}
			}
		],
		"page_count": 1,
		"current_page": 1
	},
	"msg": "接收成功"
	}

## 返回说明——4


|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情|
|info.notice|数组|星球通知|
|notice.messages.m_id | 整型| 消息ID|
|notice.users.user_id|整型|用户id|
|notice.messages.type|整型|消息类型，详情见content参数，5种|
|notice.users.user_name|字符型|用户昵称|
|notice.messages.status|整型|0未读1已读|
|notice.groups.g_name|字符型|星球名称|
|notice.groups.group_id |整型|星球ID|
|notice.content |字符串 |消息内容预览|
|notice.messages.image|字符串|用户头像或者星球头像url|
|notice.apply|数组|星球验证消息|


|参数|        类型|   说明|
|:--|:--|:--|
|info.apply|数组|私密星球申请|
|apply.messages.m_id| 整型| 消息ID|
|apply.users.user_id|整型|用户ID|
|apply.messages.text|字符型|申请理由|
|apply.messages.image|字符型|用户头像|
|apply.users.user_name|字符型|用户昵称|
|apply.groups.g_name|字符型|星球名称|
|apply.groups.group_id|字符型|星球ID|
|apply.messages.status|整型|消息是否处理1未处理2已同意3已拒绝|


|参数|        类型|   说明|
|:--|:--|:--|
|info.reply|数组|帖子通知|
|reply.users.user_id | 整型| 回复人ID|
|reply.messages.m_id | 整型| 消息ID|
|reply.posts.reply_floor|字符型|回复人楼层|
|reply.users.user_name|字符型|回复人昵称|
|reply.messages.image|字符串|回复人头像|
|reply.posts.p_title|字符串|帖子标题|
|reply.posts.post_id|整型|帖子id|
|page_count|整型|总页码，此处默认返回1
|current_page|整型|当前页码，此处默认返回1
|msg |字符串 |提示信息|


## 示例——4

显示用户id=1的消息列表星球验证消息

http://dev.wuanlife.com:800/user/show_message

	JSON:
	{
	"ret": 200,
	"data": {
		"code": 1,
		"info": {
			"notice": {
				"content": "叶寻退出了sdfdddc",
				"users": {
					"user_id": "46",
					"user_name": "叶寻"
				},
				"groups": {
					"group_id": "16",
					"g_name": "sdfdddc"
				},
				"messages": {
					"m_id": "28",
					"type": "4",
					"status": "1",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
				}
			},
			"apply": {
				"users": {
					"user_id": "58",
					"user_name": "xiaochao_php"
				},
				"groups": {
					"group_id": "358",
					"g_name": "私密星球_666"
				},
				"messages": {
					"m_id": "22",
					"status": "1",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2\/1\/w\/100\/h\/100",
					"text": "24531"
				}
			},
			"reply": {
				"users": {
					"user_id": "3",
					"user_name": "午安网"
				},
				"posts": {
					"post_id": "1",
					"p_title": "通过接口编辑",
					"reply_floor": "4",
					"page": 1
				},
				"messages": {
					"m_id": "16",
					"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
				}
			}
		},
		"page_count": 1,
		"current_page": 1
	},
	"msg": "接收成功"
	}
