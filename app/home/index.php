<?php
header("Content-Type: application/json");
echo json_encode([
  "message" => "Dokumentation unter https://github.com/Rfaria06/m295",
  "info" => "Routen: /tabellenName ||/tabellenName/spaltenName/id",
  "request" => "Anfrage auf id -> /lernende/id/22"
]);
http_response_code(200);
