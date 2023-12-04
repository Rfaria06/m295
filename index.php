<?php

use ext\DB;
use ext\Sanitize;

/**
 * @author Raul Faria
 * @version 1.0.0
 * M295 - PHP Backend
 *
 * Setting up configurations and constants
 * Routing
 */
// Enable error reporting
//      Commented out in production

//ini_set('display_errors', 'On');
//error_reporting(E_ALL & ~E_NOTICE);

// Config
define('ABSPATH', dirname(__FILE__));
$tables = ['lehrbetriebe', 'lernende', 'lehrbetrieb_lernende', 'laender', 'dozenten', 'kurse', 'kurse_lernende'];

// Load classes
require('ext/Sanitize.php');
require('ext/DB.php');

// Get request URI
$requestUrl = filter_var($_SERVER['REQUEST_URI'], FILTER_SANITIZE_URL);
$requestUrl = parse_url($requestUrl);

$path = (isset($requestUrl['path']) ? trim($requestUrl['path'], '/') : '');
$query = ($requestUrl['query'] ?? '');

define('REQUESTURI', $path);

// Routing
$requestView = '';

// Routing: home
if (REQUESTURI === '' OR REQUESTURI === 'home') {
    require_once(ABSPATH . '/app/home/index.php');
}

// Routing: other views
else {
    // Split path : get parameters and count
    $split_requesturi = explode('/', REQUESTURI);

    // Sanitize params
    $table = isset($split_requesturi[0]) ? Sanitize::sanitizeString($split_requesturi[0]) : null;
    $column = isset($split_requesturi[1]) ? Sanitize::sanitizeString($split_requesturi[1]) : null;
    $id = isset($split_requesturi[2]) ? Sanitize::sanitizeString($split_requesturi[2]) : null;

    if ($table) {
        define('TABLE', $table);
        define('COLUMN', $column);
        define('ID', $id);

        // Routing: view or error
        if (in_array(TABLE, $tables)) {
            $db = new DB(TABLE, COLUMN, ID);
        } else {
            require_once(ABSPATH . '/app/error/not_found.php');
        }

    } else {
        header('Content-Type: application/json');
        http_response_code(400);
        die(json_encode(['error' => 'No Table specified']));
    }
}