#group.posts

帖子发布-星球帖子发布

##接口调用请求说明

接口URL：http://localhost:88/index.php/group/posts

请求方式： GET&POST

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_id|整形|必须|-|用户ID|
|group_id  | 整型  | 必须   |  最小：1  |  发帖星球|
|p_title | 字符串 |必须   |  最小：1  |   帖子标题|
|p_text  | 字符串| 必须  |  最小：1 |  帖子正文|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code | 整型 | 操作码，1表示发布成功，0表示发布失败|
|post_id|整型|帖子id，此处只返回帖子id,由此跳转到帖子详情|
|msg |  字符串 |提示信息|

##示例

用户id=1在星球id=1，发表帖子

http://apihost/?service=Group.Posts

    JSON:
    {
	"ret": 200,
	"data": {
		"code": 1,
		"post_id": 45
	},
	"msg": "发表成功"
    }
