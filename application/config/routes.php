<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//保留路由
$route['default_controller'] = 'User';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//用户相关路由
$route['users/signin']['post'] = 'user/login';
$route['users']['post'] = 'user/reg';
$route['users/(:num)/checkmail']['post'] = 'user/check_mail/$1';
$route['users/checkmail']['post'] = 'user/check2mail';
$route['users/resetpsw']['post'] = 'user/send_mail';
$route['users/resetpsw']['put'] = 'user/re_psw';
$route['users/(:num)']['get'] = 'user/user_info/$1';
$route['users/(:num)']['put'] = 'user/user_info/$1';
$route['users/(:num)/messages']['get'] = 'user/show_message/$1';
$route['users/(:num)/messages/(:num)']['post'] = 'user/process_apply/$1/$2';
$route['users/(:num)/messages/(:num)']['delete'] = 'user/message/$1/$2';
$route['users/(:num)/messages/new']['get'] = 'user/check_info/$1';
$route['users/(:num)/password']['put'] = 'user/password/$1';

//星球相关路由
$route['groups']['get'] = 'group/lists';
$route['groups']['post'] = 'group/create';
$route['groups/(:num)']['get'] = 'group/group_info/$1';
$route['groups/(:num)']['put'] = 'group/group_info/$1';
$route['groups/(:num)/private']['post'] = 'group/private_group/$1';
$route['groups/(:num)/members']['get'] = 'group/member/$1';
$route['groups/(:num)/members']['post'] = 'group/join/$1';
$route['groups/(:num)/members']['delete'] = 'group/quit/$1';
$route['groups/(:num)/members/(:num)']['get'] = 'group/status/$1/$2';
$route['groups/(:num)/members/(:num)']['delete'] = 'group/member/$1/$2';

//帖子相关路由
$route['post']['get'] = 'post/index';
$route['posts/(:num)/approval']['post'] = 'post/approve';
