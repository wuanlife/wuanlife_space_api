#Post.GetPostBase

帖子详情-发帖内容

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Post.GetPostBase

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|post_id|int|必须|帖子ID|
|user_id|int|可选|用户ID|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|code|int|操作码，帖子删除为0. 正常显示为1. 私密帖子为2|
|msg|string|提示信息|
|postID	|	int	|帖子ID|
|groupID	|	int	|星球ID|
|groupName|	string	|星球名称|
|title	|string|	标题|
|text	|string|	内容|
|id|	int	|用户ID|
|lock|    int |是否锁定(0为未锁定，1为锁定)|
|approved|	int	|是否点赞(0未点赞，1已点赞)|
|approvednum|	int	|点赞数|
|nickName	|string|	发帖人|
|createTime|	date|	发帖时间|
|editRight|	boolean	|	编辑权限(0为无权限，1有)|
|deleteRight|	boolean	|	删除权限(0为无权限，1有)|
|stickyRight|	boolean	|	置顶权限(0为无权限，1有)|
|lockRight|	boolean	|	锁帖权限(0为无权限，1有)|
|collect| boolean |   帖子是否收藏(0为未收藏，1为收藏)|

##示例

显示帖子ID为1的帖子内容,此人为发帖者

http://dev.wuanlife.com:800/?service=Post.GetPostBase&post_id=3&id=2

     JSON:
    {
        "ret": 200,
        "data": {
            "postID": "3",
            "groupID": "1",
            "groupName": "装备2014中队和是加",
            "title": "hello world",
            "text": "<p>大家好 &nbsp;我是java</p>",
            "id": "3",
            "nickname": "午安网",
            "createTime": "2016-05-20 20:04:18",
            "sticky": 0,
            "lock": 0,
            "p_image": [
                []
            ],
            "collect": 0,
            "editRight": 0,
            "deleteRight": 0,
            "stickyRight": 0,
            "lockRight": 0,
            "code": 1
        },
        "msg": ""
    }
