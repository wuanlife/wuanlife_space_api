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
|messageType|整型|必须|默认1|消息分类，1回复我的，2其他通知，3私密星球申请|

##消息分类，1回复我的，2其他通知，3私密星球申请

##返回说明——1
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.userID | 整型| 回复人ID|
|info.replyfloor|字符型|回复人楼层|
|info.nickname|字符型|回复人昵称|
|info.replytext|字符型|回复内容|
|info.createTime |字符串 |创建时间|
|info.posttitle  | 字符串  |回复帖子的标题|
|info.postID|整型|回复帖子的ID|
|info.groupID |整型|回复帖子所属星球的ID|
|info.groupname|字符型|回复帖子所属星球名称|
|info.page|整型|回复内容所在的页码|
|pageCount|整型|总页码|
|currentPage|整型|当前页码|
|msg |字符串 |提示信息|

##示例——1

显示用户id=3的消息列表“回复我的”

http://dev.wuanlife.com:800/demo/?service=User.ShowMessage&user_id=3

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": [
            {
                "userID": "1",
                "replyfloor": "36",
                "nickname": "taotao",
                "replytext": "接口测试",
                "createTime": "2016-11-16 15:28:58",
                "posttitle": "我是午安熊",
                "postID": "2",
                "groupID": "2",
                "groupname": "午安网",
                "page"："2"
            }
        ],
        "pageCount": 1,
        "currentPage": 1,
        "msg": "接收成功"
    },
    "msg": ""
    }

##返回说明——2
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.id | 整型| 消息ID|
|info.nickname|字符型|操作人昵称|
|info.messagetype|字符型|消息类型，此处都是3|
|info.messageInfo|字符型|消息详情|
|info.group_id |整型|所属星球的ID|
|info.createTime |字符串 |创建时间|
|pageCount|整型|总页码
|currentPage|整型|当前页码
|msg |字符串 |提示信息|

##示例——2

显示用户id=3的消息列表“其他通知”

http://dev.wuanlife.com:800/demo/?service=User.ShowMessage&user_id=3&messageType=2

    JSON:
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": [
            {
                "id": "599",
                "nickname": "汪汪汪",
                "messagetype": "3",
                "messageInfo": "汪汪汪已将你从装备2014中队中移除",
                "group_id": "1",
                "createTime": "2016-11-16 15:55"
            },
            {
                "id": "598",
                "nickname": "taotao",
                "messagetype": "3",
                "messageInfo": "taotao已加入你的午安网",
                "group_id": "2",
                "createTime": "2016-11-16 15:53"
            },
            {
                "id": "597",
                "nickname": "taotao",
                "messagetype": "3",
                "messageInfo": "taotao已从你的午安网中退出",
                "group_id": "2",
                "createTime": "2016-11-16 15:53"
            }
        ],
        "pageCount": 1,
        "currentPage": 1,
        "msg": "接收成功"
    },
    "msg": ""
    }

##返回说明——3
|参数|        类型|   说明|
|:--|:--|:--|
|code  |  整型  |操作码，1表示接收成功，0表示没有新消息|
|info   | 数组  |用户消息列表详情,按时间降序排列|
|info.id | 整型| 消息ID|
|info.user_image|字符型|用户默认头像|
|info.nickname|字符型|用户昵称|
|info.messagetype  | 字符串  |消息类型1申请消息 2其他消息|
|info.messageInfo|数组|消息详情|
|info.messageInfo.information | 字符串| 用户消息详情|
|info.messageInfo.group_name|字符型|星球名称|
|info.messageInfo.group_id|整型|星球ID|
|info.messageInfo.status|整型|1未处理 2已同意 3已拒绝 （只有消息类型为01时，才有此字段返回）|
|info.messageInfo.text|字符串|申请信息（只有消息类型为01时，才有此字段返回）|
|info.createTime |字符串 |创建时间|
|pageCount|整型|总页码
|currentPage|整型|当前页码
|msg |字符串 |提示信息|


##示例——3

显示用户id=92的消息列表“私密星球申请”

http://dev.wuanlife.com:800/?service=User.ShowMessage&user_id=92&messageType=3

    JSON：
    {
    "ret": 200,
    "data": {
        "code": 1,
        "info": [
            {
                "id": "51",
                "nickname": "梁王test",
                "messagetype": "2",
                "messageInfo": {
                    "user_image": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
                    "information": "已拒绝你的加入",
                    "group_name": "《测试私密申请2》",
                    "group_id": "276"
                },
                "createTime": "2016-09-26 13:21"
            },
            {
                "id": "3",
                "user_image": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
                "nickname": "梁王test",
                "messagetype": "2",
                "messageInfo": {
                    "information": "已同意你的加入",
                    "group_name": "《测试私密申请2》",
                    "group_id": "276"
                },
                "createTime": "2016-09-24 15:05"
            },
            {
                "id": "22",
                "user_image": "http://7xlx4u.com1.z0.glb.clouddn.com/o_1aqt96pink2kvkhj13111r15tr7.jpg?imageView2/1/w/100/h/100",
                "nickname": "ccc",
                "messagetype": "2",
                "messageInfo": {
                    "information": "已同意你的加入",
                    "group_name": "《测试私密申请2》",
                    "group_id": "276"
                },
                "createTime": "2016-08-31 03:01"
            }
        ],
        "pageCount": 2,
        "currentPage": 1,
        "msg": "接收成功"
    },
    "msg": ""
    }