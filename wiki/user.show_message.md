#user.show_message 

用户消息中心接口-用于接收其他用户发送给用户消息

##接口调用请求说明

接口URL：http://localhost:88/index.php/user/show_message

请求方式：GET

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     ||           最小：1  |  用户ID|
|pn|整型|必须 |默认4||消息页码|
|m_type|整型|必须|默认1||消息分类，见下行|

##消息分类，1帖子通知，2星球通知，3星球验证消息，4.消息主页面

##返回说明——1
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.user_id | 整型| 回复人ID|
|info.m_id | 整型| 消息ID|
|info.reply_floor|字符型|回复人楼层|
|info.user_name|字符型|回复人昵称|
|info.profile_picture|字符串|回复人头像|
|info.p_title|字符串|帖子标题|
|info.post_id|整型|帖子id|
|page_count|整型|总页码|
|current_page|整型|当前页码|
|msg |字符串 |提示信息|

##示例——1

显示用户id=82的消息列表帖子通知

http://localhost:88/index.php/user/show_message?user_id=82&pn=1&m_type=1

    JSON:
    {
	"ret": 200,
	"data": {
		"code": 1,
		"info": [
			{
				"user_id": "58",
				"m_id": "13",
				"reply_floor": "14",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2\/1\/w\/100\/h\/100",
				"user_name": "xiaochao_php",
				"p_title": "test",
				"post_id": "5"
			},
			{
				"user_id": "58",
				"m_id": "12",
				"reply_floor": "13",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b34pfog9v161kdlkkm1kt41f697.jpg?imageView2\/1\/w\/100\/h\/100",
				"user_name": "xiaochao_php",
				"p_title": "test",
				"post_id": "5"
			}
		],
		"page_count": 1,
		"current_page": 1
	},
	"msg": "接收成功"
	}

##返回说明——2
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.m_id | 整型| 消息ID|
|info.user_id|整型|用户id|
|info.type|整型|消息类型，详情见content参数，5种|
|info.user_name|字符型|用户昵称|
|info.status|整型|0未读1已读|
|info.g_name|字符型|星球名称|
|info.group_id |整型|星球ID|
|info.content |字符串 |消息内容预览|
|info.image|字符串|用户头像或者星球头像url|
|page_count|整型|总页码
|current_page|整型|当前页码
|msg |字符串 |提示信息|

##示例——2

显示用户id=58的消息列表星球通知

http://localhost:88/index.php/user/show_message?user_id=58&pn=1&m_type=2

    JSON:
    {
	"ret": 200,
	"data": {
		"code": 1,
		"info": [
			{
				"user_id": "3",
				"m_id": "25",
				"type": "4",
				"user_name": "用户一号",
				"g_name": "星球4号",
				"group_id": "4",
				"status": "1",
				"content": "用户一号退出了星球4号",
				"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "3",
				"m_id": "146",
				"type": "3",
				"user_name": "用户一号",
				"g_name": "星球3号",
				"group_id": "3",
				"status": "1",
				"content": "你被移出了星球星球3号",
				"image": "http:\/\/image.suxiazai.com\/img\/pic\/960\/359\/11680961633.jpg"
			},
			{
				"user_id": "3",
				"m_id": "145",
				"type": "2",
				"user_name": "用户一号",
				"g_name": "星球2号",
				"group_id": "2",
				"status": "1",
				"content": "拒绝了你的加入申请星球2号",
				"image": "http:\/\/image.suxiazai.com\/img\/pic\/960\/359\/11680961633.jpg"
			}
			{
				"user_id": "3",
				"m_id": "135",
				"type": "1",
				"user_name": "用户一号",
				"g_name": "装备2014中队",
				"group_id": "1",
				"status": "1",
				"content": "同意了你的加入申请装备2014中队",
				"image": "http:\/\/image.suxiazai.com\/img\/pic\/960\/359\/11680961633.jpg"
			},
			{
				"user_id": "3",
				"m_id": "18",
				"type": "5",
				"user_name": "用户一号",
				"g_name": "星球11号",
				"group_id": "11",
				"status": "1",
				"content": "用户一号加入了星球11号",
				"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100"
			}
		],
		"page_count": 1,
		"current_page": 1
	},
	"msg": "接收成功"
	}

##返回说明——3
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.m_id | 整型| 消息ID|
|info.user_id|整型|用户ID|
|info.text|字符型|申请理由|
|info.profile_picture|字符型|用户头像|
|info.user_name|字符型|用户昵称|
|info.g_name|字符型|星球名称|
|info.status|整型|消息是否处理1未处理2已同意3已拒绝|
|page_count|整型|总页码
|current_page|整型|当前页码
|msg |字符串 |提示信息|


##示例——3

显示用户id=58的消息列表星球验证消息

http://localhost:88/index.php/user/show_message?user_id=58&pn=1&m_type=3

    JSON：
    {
	"ret": 200,
	"data": {
		"code": 1,
		"info": [
			{
				"user_id": "3",
				"m_id": "22",
				"text": "24531",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100",
				"user_name": "用户一号",
				"g_name": "私密星球_666",
				"group_id": "358",
				"status": "2"
			},
			{
				"user_id": "3",
				"m_id": "19",
				"text": "测试中  请稍后",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100",
				"user_name": "用户一号",
				"g_name": "私密星球_666",
				"group_id": "358",
				"status": "1"
			}
		],
		"page_count": 1,
		"current_page": 1
	},
	"msg": "接收成功"
	}

##返回说明——4
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情|
|info.notice|数组|星球通知|
|notice.m_id | 整型| 消息ID|
|notice.user_id|整型|用户id|
|notice.type|整型|消息类型，详情见content参数，5种|
|notice.user_name|字符型|用户昵称|
|notice.status|整型|0未读1已读|
|notice.g_name|字符型|星球名称|
|notice.group_id |整型|星球ID|
|notice.content |字符串 |消息内容预览|
|notice.image|字符串|用户头像或者星球头像url|
|notice.apply|数组|星球验证消息|
||||
|info.notice|数组|星球通知|
|apply.m_id | 整型| 消息ID|
|apply.user_id|整型|用户ID|
|apply.text|字符型|申请理由|
|apply.profile_picture|字符型|用户头像|
|apply.user_name|字符型|用户昵称|
|apply.g_name|字符型|星球名称|
|apply.status|整型|消息是否处理1未处理2已同意3已拒绝|
||||
|info.reply|数组|帖子通知|
|reply.user_id | 整型| 回复人ID|
|reply.m_id | 整型| 消息ID|
|reply.reply_floor|字符型|回复人楼层|
|reply.user_name|字符型|回复人昵称|
|reply.profile_picture|字符串|回复人头像|
|reply.p_title|字符串|帖子标题|
|reply.post_id|整型|帖子id|
|page_count|整型|总页码，此处默认返回1
|current_page|整型|当前页码，此处默认返回1
|msg |字符串 |提示信息|


##示例——4

显示用户id=58的消息列表星球验证消息

http://localhost:88/index.php/user/show_message?user_id=58&pn=1&m_type=4

	JSON:
	{
	"ret": 200,
	"data": {
		"code": 1,
		"info": {
			"notice": {
				"user_id": "3",
				"m_id": "25",
				"type": "4",
				"user_name": "用户一号",
				"g_name": "星球4号",
				"group_id": "4",
				"status": "1",
				"content": "用户一号退出了星球4号",
				"image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			"apply": {
				"user_id": "3",
				"m_id": "22",
				"text": "24531",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100",
				"user_name": "用户一号",
				"g_name": "私密星球_666",
				"group_id": "358",
				"status": "2"
			},
			"reply": {
				"user_id": "3",
				"m_id": "16",
				"reply_floor": "4",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100",
				"user_name": "用户一号",
				"p_title": "通过接口编辑",
				"post_id": "1"
			}
		},
		"page_count": 1,
		"current_page": 1
	},
	"msg": "接收成功"
	}

