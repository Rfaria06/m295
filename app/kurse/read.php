<?php
use ext\DB;

// get database instance
$db = new DB();
$conn = $db->getConn();

if (REQUESTID === 'all')
{
    $query = 'SELECT * FROM tbl_kurse;';
    //$row = $conn->query($query)->fetch_assoc();
    //echo json_encode($row);
}