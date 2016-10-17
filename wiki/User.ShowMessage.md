#User.ShowMessage

用户消息中心接口-用于接收其他用户发送给用户消息

##接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=User.ShowMessage

请求方式：POST

参数说明：

|参数名字   | 类型|  是否必须   | 默认值   | 范围      |  说明|
|:--|:--|:--|:--|:--|:--|
|user_id    |   整型| 必须     ||           最小：1  |  用户ID|
|pn|整型||默认1|消息页码|
|status|整型|必须|默认1|1全部2已读3未读|


##返回说明
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.id | 整型| 消息ID|
|info.nickname|字符型|用户昵称|
|info.user_image|字符型|用户默认头像|
|info.group_name|字符型|星球名称|
|info.group_id|整型|星球ID|
|info.information | 字符串| 用户消息详情|
|info.createTime |字符串 |创建时间|
|info.status|整型|1未处理 2已同意 3已拒绝 （只有消息类型为01时，才有此字段返回）
|info.messagetype  | 字符串  |消息类型1申请消息 2其他消息|
|pageCount|整型|总页码
|currentPage|整型|当前页码
|msg |字符串 |提示信息|


##示例

显示用户id=1的消息列表

http://dev.wuanlife.com:800/?service=User.ShowMessage&user_id=92

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": [
            {
                "id": "255",
                "nickname": "奇奇",
                "information": "申请加入",
                "group_name": "《儿时22》",
                "group_id": "22",
                "createTime": "2016-10-10 14:59",
                "status": "1",
                "messagetype": "1",
                "text": null
            },
            {
                "id": "254",
                "nickname": "奇奇",
                "information": "申请加入",
                "group_name": "《儿时22》",
                "group_id": "22",
                "createTime": "2016-10-10 14:43",
                "status": "1",
                "messagetype": "1",
                "text": null
            },
            {
                "id": "227",
                "nickname": "梁王test",
                "information": "已同意你的加入",
                "group_name": "《测试私密申请2》",
                "group_id": "276",
                "createTime": "2016-10-09 15:30",
                "messagetype": "2"
            },
            {
                "id": "225",
                "nickname": "梁王test",
                "information": "已拒绝你的加入",
                "group_name": "《测试私密申请2》",
                "group_id": "276",
                "createTime": "2016-10-09 15:27",
                "messagetype": "2"
            }
        ],
        "pageCount": 2,
        "currentPage": 1,
        "msg": "接收成功"
    },
    "msg": ""
}