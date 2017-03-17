# group.get_collect_post

获取用户收藏帖子的接口

## 接口调用请求说明

接口URL：http://dev.wuanlife.com:800/?service=Post.GetCollectPost

请求方式：GET

参数说明：

|参数名字        |类型  |是否必须    |默认值    |范围                   |说明|
|:--|:--|:--|:--|:--|:--|
|user_id        |整型   |必须          ||                              |用户id|
|pn       |整型   |可选          | 1   |                          |当前页面|

## 返回说明

|返回字段                | 类型   |     说明|
|:--|:--|:--|
|posts.post_id  |      int   |   帖子id|
|posts.create_time       |       string    |  收藏时间|
|posts.p_name           |     string   |      帖子标题|
|posts.group_id    |      int   |   帖子所属星球id|
|posts.g_name |   string   |   帖子所属星球名称|
|posts.user_name        |       string     |    发帖者|
|posts.delete        |       int     |    帖子是否被删除|
|page_count           |     int     |    总页数|
|current_page        |      int   |      当前页|

## 示例

显示用户ID为1所加入的星球

http://dev.wuanlife.com:800/?service=Post.GetCollectPost&user_id=14

    JSON
    {
        "ret": 200,
        "data": {
            "pageCount": 1,
            "currentPage": 1,
            "posts": [
                {
                    "createTime": "1479563292",
                    "post_name": "午安煎饼计划Android组第48周周报",
                    "gbID": "2",
                    "groupName": "午安网啊阿萨阿萨安师大",
                    "user_name": "午安网",
                    "delete": "0"
                },
                {
                    "createTime": "1479560765",
                    "post_name": "hello world",
                    "gbID": "1",
                    "groupName": "装备2014中队和是加",
                    "user_name": "午安网",
                    "delete": "0"
                }
            ]
        },
        "msg": ""
    }
