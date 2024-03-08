<?php 

function addJobToQueue($type, $data) {
    global $_MYSQL;

    $_MYSQL->begin_transaction();

    runSQL([
        "INSERT INTO jobs (uuid, type, data) VALUES ('".generateUUIDv4()."', '$type', '".json_encode($data)."')",
    ], [
        'cache' => false,
        'cache_ttl' => false,
        'silence_duplicate_entry' => true,
        'silence_empty_select' => true,
    ]);
    
    $_MYSQL->commit();

}
