<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There is one reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
*/

$route['default_controller']                    	 = 'page';

// Manage
$route['manage']								 	 = 'manage/index';
$route['ajax/(:any)']							     = 'ajax/$1';

/**
 * Login
 */
$route['login']								    	 = 'page/login';
$route['join']                                  	 = 'page/join';

$route['login/facebook']                        	 = 'user/login_facebook';
$route['logout']									 = 'user/logout';

$route['login/do']                              	 = 'user/login';
$route['join/do']                               	 = 'user/join';

/* 
 * site by permalink
 */
$route['(:any)/manage/place/edit/(:num)']			 		= 'manage/place_edit/site:$1/$2';
$route['(:any)/manage/image/edit/(:num)']			 		= 'manage/place_edit/site:$1/$2';
$route['(:any)/manage/place/delete/(:num)']			 		= 'manage/place_delete/site:$1/$2';
$route['(:any)/manage/place/change/(:any)/(:num)/(:any)']	= 'manage/place_change/site:$1/$2/$3/$4';

$route['(:any)/manage/course/edit/(:num)']			 		= 'manage/course_edit/site:$1/$2';
$route['(:any)/manage/course/change/(:any)/(:num)/(:any)']	= 'manage/course_change/site:$1/$2/$3/$4';

$route['(:any)/manage/list/(:any)']					 = 'manage/lists/site:$1/$2';
$route['(:any)/manage/list']						 = 'manage/lists/site:$1';

$route['(:any)/manage/type/add/(:any)/(:num)']       = 'manage/type_add/site:$1/$2/$3';
$route['(:any)/manage/type/add/(:any)']				 = 'manage/type_add/site:$1/$2';
$route['(:any)/manage/type/delete/(:num)']		     = 'manage/type_delete/site:$1/$2';
$route['(:any)/manage/type/edit/(:num)']		     = 'manage/type_edit/site:$1/$2';

$route['(:any)/(:any)/(:any)/(:any)/(:any)/(:any)']	 = '$2/$3/site:$1/$4/$5/$6';
$route['(:any)/(:any)/(:any)/(:any)/(:any)']		 = '$2/$3/site:$1/$4/$5';
$route['(:any)/(:any)/(:any)/(:any)']				 = '$2/$3/site:$1/$4';
$route['(:any)/(:any)/(:any)']						 = '$2/$3/site:$1';
$route['(:any)/(:any)']								 = '$2/index/site:$1';
$route['(:any)']									 = 'page/index/site:$1';


/* End of file routes.php */
/* Location: ./application/config/routes.php */