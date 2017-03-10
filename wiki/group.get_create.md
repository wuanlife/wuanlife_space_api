#group.get_create

获取用户创建星球的接口

##接口调用请求说明

接口URL：http://localhost:88/index.php/group/get_create

请求方式：GET

参数说明：

|参数名字        |类型  |是否必须    |默认值    |范围                   |说明|
|:--|:--|:--|:--|:--|:--|
|user_id        |整型   |必须          ||                              |用户id|
|pn       |整型   |可选          | 1   |                              |当前页面|

##返回说明

|返回字段                | 类型   |     说明|
|:--|:--|:--|
|groups.g_name       |       string    |  星球名称|
|groups.group_id           |     int   |      星球id|
|groups.g_image     |      string   |   星球图片|
|groups.g_introduction |   string   |   星球介绍|
|group.num         |       int     |    成员数量|
|page_count           |     int     |    总页数|
|current_page        |      int   |      当前页|
|num|整型|星球数量|
|user_name|字符型|用户昵称|
|msg|字符型|提示信息|

##示例

显示用户ID为58创建的星球

http://localhost:88/index.php/group/get_create?user_id=58

    JSON
    {
	"ret": 200,
	"data": {
		"groups": [
			{
				"g_name": "装备2014中队",
				"group_id": "1",
				"g_image": "http:\/\/image.suxiazai.com\/img\/pic\/960\/359\/11680961633.jpg",
				"g_introduction": "1$g_image=1",
				"num": "12"
			},
			{
				"g_name": "私密星球_666",
				"group_id": "358",
				"g_image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100",
				"g_introduction": "",
				"num": "2"
			}
		],
		"page_count": 1,
		"current_page": 1,
		"num": 10,
		"user_name": "xiaochao_php"
	},
	"msg": "获取星球列表成功"
    }
