<?php

// enable error reporting
error_reporting(E_ALL & ~E_NOTICE);

// config
const APPNAME = 'Kursverwaltung';
define("ABSPATH", dirname(__FILE__));
const ABSURL = 'https://modul295.pr24.dev';

// load classes
require('ext/sanitize.php');
require('ext/db.php');

// get request uri
$requestUrl = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
$requestUrl = parse_url($requestUrl);

$path = (isset($requestUrl['path']) ? trim($requestUrl['path'], '/') : '');
$query = ($requestUrl['query'] ?? '');

define('REQUESTURI', $path);
define('REQUESTQUERY', $query);

// db connection
$pdo = DB::getPdo();

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
    //echo '<pre>'.print_r($split_requesturi, true).'</pre>';

    // sanitize params
    $route_folder = (isset($split_requesturi[0]) && !preg_match('/[^A-Za-z0-9_]/', $split_requesturi[0]) ? $split_requesturi[0] : '');
    $route_id = (isset($split_requesturi[1]) && !preg_match('/[^0-9]/', $split_requesturi[1]) ? $split_requesturi[1] : '');

    // 1 parameters in the query string : index, create
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