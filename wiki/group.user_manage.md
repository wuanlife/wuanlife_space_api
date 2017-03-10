#group.user_manage

星球用户管理接口-用于显示加入星球的用户，方便管理

##接口调用请求说明

接口URL：http://localhost:88/index.php/group/user_manage

请求方式：GET

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id|整型|必须|||用户ID|
|group_id|整型|必须|||星球ID|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，0表示没有权限或者成员数为零|
|users   | 数组  |用户信息详情|
|users.user_id | 整型| 用户ID|
|users.user_name | 字符串| 用户昵称|
|users.profile_picture|字符串|用户头像|
|group_id|整型|星球id，输入参数就有，为什么还要回传？|
|msg |字符串 |提示信息|


##示例

显示星球id=1的用户列表

http://localhost:88/index.php/group/user_manage?user_id=1&group_id=1

    JSON：
    {
	"ret": 200,
	"data": {
		"group_id": "1",
		"users": [
			{
				"user_id": "3",
				"user_name": "用户一号",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1b2q9g8uv1o1a125n7qs1btq1glk7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "37",
				"user_name": "人人献出一片爱",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "46",
				"user_name": "叶寻",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "51",
				"user_name": "777",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "56",
				"user_name": "qwer",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "71",
				"user_name": "xinbaobao214",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "78",
				"user_name": "asdfasdf",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "86",
				"user_name": "汪汪3",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "118",
				"user_name": "qq.com",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			},
			{
				"user_id": "131",
				"user_name": "??",
				"profile_picture": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100"
			}
		],
		"code": 1
	},
	"msg": "显示成功！"
	}