# group.search

搜索接口

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/group/search

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|text|string|必须|搜索内容|
|pnum|int|可选|帖子每页数量，为0时不查询帖子|
|pn|int|可选|帖子当前页数|
|gnum|int|可选|星球每页数量，为0时不查询星球|
|gn|int|可选|星球当前页数|


## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|group.g_name           |字符型   |星球名称|
|group.g_image           |字符型   |星球图片|
|group.g_introduction           |字符型   |星球介绍|
|group.group_id     |整型 |星球ID|
|group.num           |整型 |星球成员数|
|group_page          |整型 |星球总页数|
|g_current_page|整型|星球当前页数|
|posts.post_id   |   int|    帖子ID|
|posts.p_title|   string| 标题|
|posts.p_text |string |内容|
|posts.create_time|  date|   发帖时间|
|posts.user_name|    string  |发帖人|
|posts.group_id| int |星球ID|
|posts.lock|    int |是否锁定|
|posts.g_name|   string| 星球名称|
|posts_page          |整型 |帖子总页数|
|p_current_page|整型|帖子当前页数|
|msg|null|提示信息，此处NULL|



## 示例

查询字符串等于1的星球和帖子结果

http://dev.wuanlife.com:800/group/search?text=1&gnum=2&pnum=2&gn=1&pn=1
    
	JSON:
    {
	"ret": 200,
	"data": {
		"group": [
			{
				"g_name": "装备2014中队",
				"group_id": "1",
				"g_image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100",
				"g_introduction": "1$g_image=1",
				"num": "11"
			},
			{
				"g_name": "星球11号",
				"group_id": "11",
				"g_image": "http:\/\/7xlx4u.com1.z0.glb.clouddn.com\/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2\/1\/w\/100\/h\/100",
				"g_introduction": null,
				"num": "8"
			}
		],
		"group_page": 39,
		"g_current_page": 1,
		"posts": [
			{
				"post_id": "16",
				"p_title": "1",
				"p_text": "1",
				"lock": "0",
				"create_time": "2017-03-03 13:34:57",
				"user_name": "xjkui",
				"group_id": "166",
				"g_name": "叶氏春秋"
			},
			{
				"post_id": "9",
				"p_title": "1",
				"p_text": "1",
				"lock": "0",
				"create_time": "2017-03-03 13:34:20",
				"user_name": "xjkui",
				"group_id": "166",
				"g_name": "叶氏春秋"
			}
		],
		"posts_page": 10,
		"p_current_page": 1
	},
	"msg": null
    }
