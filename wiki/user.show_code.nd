# user.show_code

邀请码接口-用于查看用户邀请码

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/user/show_code

请求方式：POST

参数说明：

|参数|类型|是否必须|范围|说明|
|:--|:--|:--|:--|:--|
|user_id| 整型 |   必须   |-| 用户id|

## 返回说明

|参数|类型|说明|
|:--|:--|:--|
|msg |   字符串 |提示信息|
|i_code | 字符型 |  邀请码|
|num|整型|邀请码剩余使用次数,当前版本默认99|

## 示例

查看用户id=78邀请码

http://dev.wuanlife.com:800/user/show_code

    JSON:
    {
	"ret": 200,
	"data": {
		"i_code": "uesvmw",
		"num": "99"
	},
	"msg": "查询成功"
    }