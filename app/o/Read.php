<?php

namespace app\o;

class Read
{
    public static function getFullTable($folder, $mysqli)
    {
        $query = 'SELECT * FROM '. $folder.';';
        return $mysqli->query($query);
    }
}