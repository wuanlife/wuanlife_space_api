# group.delete_group_member

星球设置成员管理接口-用户删除星球成员

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/group/delete_group_member

请求方式：GET

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id|整型|必须|-|-|用户ID，创建者|
|group_id|整型|必须|-|-|星球ID|
|member_id|整型|必须|-|-|成员ID|


## 返回说明

|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示删除成功，0表示删除失败|
|msg |字符串 |提示信息|


## 示例

删除星球id=1的成员id=3

http://dev.wuanlife.com:800/group/delete_group_member?user_id=1&group_id=1&member_id=3

    JSON：
    {
	"ret": 200,
	"data": {
		"code": 1
	},
	"msg": "操作成功！并通知被删除的成员"
	}
