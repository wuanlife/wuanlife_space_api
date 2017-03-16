# post.get_post_reply

帖子详情-回帖内容

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/post/get_post_reply

请求方式：GET

参数说明：

|参数|类型|是否必须|默认值|说明|
|:--|:--|:--|:--|:--|
|post_id|int	|必须|	-	|帖子ID|
|user_id|int    |可选|    -   |用户ID|
|pn	|int	|可选|	1|	第几页|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|reply.p_text|string	|回复内容
|reply.user_id	|int|	回帖人ID|
|reply.user_name	|string|	回帖人昵称|
|reply.reply_id	|int|	被回复人ID，为NULL代表回复楼主|
|reply.reply_nick_name	|string|被回复人昵称，为NULL代表回复楼主|
|reply.create_time|	date|	回帖时间|
|reply.p_floor|  int|   帖子楼层|
|reply.approved|	int	|是否点赞(0未点赞，1已点赞)|
|reply.approvednum|	int	|点赞数|
|reply.delete_right|  int|   删除权限（1为有此权限）|
|post_id|		int	|帖子ID|
|reply_count	|int|回帖数|
|page_count	|int	|总页数|
|current_page	|int|	当前页|

## 示例

显示帖子ID为1用户id为1的第2页回复

http://dev.wuanlife.com:800/post/get_post_reply?post_id=1

    JSON:
    {
	"ret": 200,
	"data": {
		"post_id": "1",
		"reply_count": 4,
		"page_count": 1,
		"current_page": 1,
		"reply": [
			{
				"reply_floor": "0",
				"p_text": "2333",
				"user_id": "58",
				"user_name": "xiaochao_php",
				"reply_id": null,
				"reply_nick_name": null,
				"create_time": "2017-02-14 10:12:01",
				"p_floor": "2",
				"approved": "0",
				"approvednum": "1",
				"delete_right": 0
			},
			{
				"reply_floor": "0",
				"p_text": "哈哈哈",
				"user_id": "3",
				"user_name": "用户一号",
				"reply_id": null,
				"reply_nick_name": null,
				"create_time": "2017-03-01 20:19:10",
				"p_floor": "3",
				"approved": "0",
				"approvednum": "0",
				"delete_right": 0
			}
		]
	},
	"msg": "帖子回复显示成功"
    }
