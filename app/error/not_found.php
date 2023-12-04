<?php
/**
 * this response is sent when the requested resource is not found
 */
header('Content-Type: application/json');
http_response_code(404);
echo json_encode([
    'status' => 404,
    'error' => 'Resource not found'
]);
