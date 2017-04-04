# group.get_collect_post

获取用户收藏帖子的接口

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/post/get_collect_post

请求方式：GET

参数说明：

|参数名字        |类型  |是否必须    |默认值    |范围                   |说明|
|:--|:--|:--|:--|:--|:--|
|user_id |整型   |必须          |-| -|用户id|
|pn       |整型   |可选          | 1   |- |当前页面|

## 返回说明

|返回字段                | 类型   |     说明|
|:--|:--|:--|
|posts.post_id  |      int   |   帖子id|
|posts.create_time       |       string    |  收藏时间|
|posts.p_title |     string   |      帖子标题|
|posts.group_id    |      int   |   帖子所属星球id|
|posts.g_name |   string   |   帖子所属星球名称|
|posts.user_name        |       string     |    发帖者|
|posts.delete        |       int     |    帖子是否被删除|
|posts.p_text|string|帖子内容|
|page_count           |     int     |    总页数|
|current_page        |      int   |      当前页|

## 示例

显示用户ID为1所收藏的帖子

http://dev.wuanlife.com:800/post/get_collect_post?user_id=1

    JSON
    {
    "ret": 200,
    "data": {
        "page_count": 3,
        "current_page": 1,
        "posts": [
            {
                "post_id": "54",
                "create_time": "2016-11-27 19:52:06",
                "p_title": "星球名称又截断了",
                "group_id": "104",
                "g_name": "一二三四五上山打老虎老",
                "user_name": "一二三四五六七八九十一二三四五六七",
                "delete": "0",
                "p_text": "醉了"
            },
            {
                "post_id": "55",
                "create_time": "2016-11-27 19:53:46",
                "p_title": "asdf ",
                "group_id": "104",
                "g_name": "一二三四五上山打老虎老",
                "user_name": "123123",
                "delete": "0",
                "p_text": "dsfasdffadsfs"
            }
        ]
    },
    "msg": null
    }
