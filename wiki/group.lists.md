# group.lists

星球列表-按成员数降序显示星球列表

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/group/lists

请求方式：GET

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--:|:--|
|pn|整型|可选|-|当前页数|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|groups                |整型  |星球列表对象|
|groups.g_name           |整型   |星球名称|
|groups.g_image           |字符型   |星球图片|
|groups.g_introduction           |字符型   |星球介绍|
|groups.group_id     |整型 |星球ID|
|groups.num           |整型 |星球成员数|
|page_count           |整型 |总页数|
|current_page           |整型 |当前页|
|msg|字符型|提示信息|


## 示例

显示第1页星球列表

http://dev.wuanlife.com:800/group/lists

    JSON:
    {
	"ret": 200,
	"data": {
		"groups": [
			{
				"g_name": "星球2号",
				"group_id": "2",
				"g_image": "http:\/\/image.suxiazai.com\/img\/pic\/960\/359\/11680961633.jpg",
				"g_introduction": "",
				"num": "23"
			},
			{
				"g_name": "测试29",
				"group_id": "29",
				"g_image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100",
				"g_introduction": "测试29",
				"num": "15"
			},
			{
				"g_name": "装备2014中队",
				"group_id": "1",
				"g_image": "http:\/\/image.suxiazai.com\/img\/pic\/960\/359\/11680961633.jpg",
				"g_introduction": "1$g_image=1",
				"num": "12"
			}
		],
		"page_count": 19,
		"current_page": 1
	},
	"msg": "获取星球列表成功"
    }
