#user.process_apply

处理申请者加入私密星球的申请接口-用于同意或者拒绝申请人加入私密星球

##接口调用请求说明

接口URL：http://localhost:88/index.php/user/process_apply

请求方式：GET

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |整型 |必须 ||   最小：1   |用户ID|
|m_id|  整型  |必须||        最小：1|  消息ID|
|mark   |整型 |必须 |   |   标识符，1为同意，0为拒绝|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|msg   |  字符串 |提示信息|
|code  |整型 |  操作码，1表示操作成功，0表示操作失败|


##示例

发送验证码到邮箱

http://localhost:88/index.php/user/process_apply?user_id=58&mark=1&m_id=15

    JSON:
    {
	"ret": 200,
	"data": {
		"code": 1
	},
	"msg": "操作成功！您已同意该成员的申请！"
    }
