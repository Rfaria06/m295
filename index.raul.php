<?php

// Import classes
use app\o\Read;
use ext\db;
use ext\sanitize;

// Enable error reporting
ini_set('display_errors', 'On');
error_reporting(E_ALL & ~E_NOTICE);

// Config
const APPNAME = 'Kursverwaltung';
define("ABSPATH", dirname(__FILE__));
const ABSURL = 'https://modul295.pr24.dev';

// Set content type
header('Content-Type: application/json');

// Load classes
require('ext/sanitize.php');
require_once('ext/db.php');

// Get request URI
$requestUrl = parse_url(filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL));
$path = (isset($requestUrl['path']) ? trim($requestUrl['path'], '/') : '');
$query = ($requestUrl['query'] ?? '');

define('REQUESTURI', $path);
define('REQUESTQUERY', $query);

// DB connection
$db = new db();
$conn = $db->getConnection();

// Routing
$requestView = '';

// Routing: home
if (REQUESTURI === '' OR REQUESTURI === 'home') {
    $requestView = ABSPATH . '/app/home/index.raul.php';
} else {
    // Split path: get parameters and count
    $split_requesturi = explode('/', REQUESTURI);

    // sanitize params
    $route_folder = sanitize::sanitizeRouteFolder($split_requesturi[0]);
    $route_id = sanitize::sanitizeRouteId($split_requesturi);

    // 1 parameter in the query string: index, create
    if (count($split_requesturi) === 1) {
        define('REQUESTID', 'all');

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $requestView = ABSPATH . '/app/' . $route_folder . '/read.php';
                echo json_encode(['status' => 200, 'data' => Read::getFullTable($route_folder, $conn)]);
                break;
            case 'POST':
                $requestView = ABSPATH . '/app/' . $route_folder . '/create.php';
                echo json_encode(['status' => 200, 'data' => $requestView]);
                break;
        }
    }

    // 2 parameters in the query string: read, update, delete
    else if (count($split_requesturi) === 2) {
        define('REQUESTID', $route_id);

        switch ($_SERVER['REQUEST_METHOD']) {
            case 'GET':
                $requestView = ABSPATH . '/app/' . $route_folder . '/read.php';
                echo json_encode(['status' => 200, 'data' =>$requestView]);
                break;
            case 'PUT':
                $requestView = ABSPATH . '/app/' . $route_folder . '/update.php';
                echo json_encode(['status' => 200, 'data' => $requestView]);
                break;
            case 'DELETE':
                $requestView = ABSPATH . '/app/' . $route_folder . '/delete.php';
                echo json_encode(['status' => 200, 'data' => $requestView]);
                break;
        }
    }
}

// Routing: view or error
if (file_exists($requestView)) {
    require_once($requestView);
} else {
    require_once(ABSPATH . '/app/error/not_found.php');
}
