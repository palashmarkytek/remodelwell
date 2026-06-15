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
$route['default_controller'] = 'auth/login';
$route['auth/new-user'] = 'auth/new_user';
// UPDATED: First-login Leadspedia loader and AJAX processor routes.
$route['auth/leadspedia-loader'] = 'auth/leadspedia_loader';
$route['auth/process-leadspedia'] = 'auth/process_leadspedia';
$route['cron_project'] = 'cron/backlog_old_task_and_generate_new_task';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

$route['bsr/(:any)'] = 'customer_form/index/$1';
$route['bsr/upload/(:any)'] = 'customer_form/upload/$1';
$route['bsr/delete_uploaded_file/(:any)'] = 'customer_form/delete_uploaded_file/$1';
$route['projects/(:num)'] = 'projects/index/$1';




// UPDATED: Leadspedia vertical listing and AJAX sync routes.
$route['verticals'] = 'verticals/index';
$route['verticals/sync'] = 'verticals/sync';

// UPDATED: Advertiser-only mapped vertical listing, contract detail, and status endpoint.
$route['user_verticals'] = 'user_verticals/index';
$route['user_verticals/contract/(:num)'] = 'user_verticals/contract/$1';
$route['user_verticals/change_status'] = 'user_verticals/change_status';

// UPDATED: Admin contract detail stays under the existing advertiser mapping URL.
$route['admin/mapping/(:num)/contract/(:num)'] = 'admin/mapping_contract/$1/$2';
