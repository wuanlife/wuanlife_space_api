#Post.GetPostBase

帖子详情-发帖内容

##接口调用请求说明

接口URL：http://apihost/?service=Post.GetPostBase

请求方式：GET

参数说明：

|参数|类型|是否必须|说明|
|:--|:--|:--|:--|
|post_id|int|必须|帖子ID|
|id|int|可选|用户ID|

##返回说明
|参数|类型|说明|
|:--|:--|:--|
|postID	|	int	|帖子ID|
|groupID	|	int	|星球ID|
|groupName|	string	|星球名称|
|title	|string|	标题|
|text	|string|	内容|
|id|	int	|用户ID|
|lock|    int |是否锁定|0为未锁定，1为锁定|
|nickName	|string|	发帖人|
|createTime|	date|	发帖时间|
|editRight|	boolean	|	编辑权限(0为无权限，1有)|
|deleteRight|	boolean	|	删除权限(0为无权限，1有)|
|stickyRight|	boolean	|	置顶权限(0为无权限，1有)|
|lockRight|	boolean	|	suot权限(0为无权限，1有)|

##示例

显示帖子ID为1的帖子内容,此人为发帖者

http://apihost/?service=Post.GetPostBase&post_id=1&id=1

     JSON:
     {
    "ret": 200,
    "data": {
        "postID": "1",
        "groupID": "1",
        "title": "title1",
        "text": "1texttexttexttexttexttexttexttexttexttext",
        "id": "1",
        "nickname": "陶陶1",
        "createTime": "2016-04-06 00:00:00",
        "editRight": 1
     },
    "msg": ""
     }
