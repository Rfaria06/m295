<?php

use ext\Sanitize;

// enable error reporting
error_reporting(E_ALL & ~E_NOTICE);

// config
define('ABSPATH', dirname(__FILE__));

// load classes
require('ext/Sanitize.php');

// get request uri
$requestUrl = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
$requestUrl = parse_url($requestUrl);

$path = (isset($requestUrl['path']) ? trim($requestUrl['path'], '/') : '');
$query = ($requestUrl['query'] ?? '');

define('REQUESTURI', $path);
define('REQUESTQUERY', $query);


// routing
$requestView = '';

// routing: home
if(REQUESTURI === '' OR REQUESTURI === 'home')
{
    	$requestView = ABSPATH.'/app/home/index.php';
}

// routing: other views
else
{
    	// split path : get parameters and count
	$split_requesturi = explode('/', REQUESTURI);

	// sanitize params
	$route_folder = Sanitize::sanitizeRouteFolder($split_requesturi[0]);
	$route_id = Sanitize::sanitizeRouteId($split_requesturi[1]);

	// 1 parameter in the query string : index, create
	if(count($split_requesturi) === 1)
	{
    		if($_SERVER['REQUEST_METHOD'] === 'GET')
		{
        define('REQUESTID', 'all');
        			$requestView = ABSPATH.'/app/'.$route_folder.'/read.php';
		}
		else if($_SERVER['REQUEST_METHOD'] === 'POST')
        		{
        			$requestView = ABSPATH.'/app/'.$route_folder.'/create.php';
		}
	}
	
	// 2 parameters in the query string : read, update, delete
	else if(count($split_requesturi) === 2)
    	{
    		define('REQUESTID', $route_id);
    		
		if($_SERVER['REQUEST_METHOD'] === 'GET')
		{
        			$requestView = ABSPATH.'/app/'.$route_folder.'/read.php';
		}
		else if($_SERVER['REQUEST_METHOD'] === 'PUT')
        		{
        			$requestView = ABSPATH.'/app/'.$route_folder.'/update.php';
		}
		else if($_SERVER['REQUEST_METHOD'] === 'DELETE')
        		{
        			$requestView = ABSPATH.'/app/'.$route_folder.'/delete.php';
		}
	}
}

// routing: view or error
if(file_exists($requestView))
{
    	require_once($requestView);
}
else
{
    	require_once(ABSPATH.'/app/error/not_found.php');
}