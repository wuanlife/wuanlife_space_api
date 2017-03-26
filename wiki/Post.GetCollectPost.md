# group.get_collect_post

获取用户收藏帖子的接口

## 接口调用请求说明

接口URL：http://localhost/wuanlife_api/index.php/Post/get_collect_post

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
|posts.post_name           |     string   |      帖子标题|
|posts.gb_id    |      int   |   帖子所属星球id|
|posts.group_name |   string   |   帖子所属星球名称|
|posts.user_name        |       string     |    发帖者|
|posts.delete        |       int     |    帖子是否被删除|
|page_count           |     int     |    总页数|
|current_page        |      int   |      当前页|

## 示例

显示用户ID为1所收藏的帖子

http://localhost/wuanlife_api/index.php/Post/get_collect_post?user_id=1

    JSON
    {
        "ret": 200,
        "data": {
            "pageCount": 1,
            "currentPage": 1,
            "posts": [
                {
                    "post_id": "1",
                    "create_time": "2017-03-21 13:46:57",
                    "post_name": "通过接口编辑",
                    "gb_id": "2",
                    "group_name": "午安网",
                    "user_name": "xiaochao_php",
                    "delete": "1"
                },
                {
                    "post_id": "11",
                    "create_time": "2017-03-18 07:13:35",
                    "post_name": "1",
                    "gb_id": "166",
                    "group_name": "叶氏春秋",
                    "user_name": "xjkui",
                    "delete": "0"
                }
            ]
        },
        "msg": null
    }
