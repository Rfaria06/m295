<?php
header('Content-Type: application/json');
echo json_encode(['message' => 'Home']);
http_response_code(200);