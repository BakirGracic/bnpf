<?php

function runRedis($func, $command) {
    global $_REDIS;

    if (!method_exists($_REDIS, $func)) {
        throw new Exception("Redis Builder: Unknown Redis function -> $func");
    }

    try {
        $res = call_user_func_array([$_REDIS, $func], $command);
    } catch (\Throwable $th) {
        response(500, ['error' => DebugErrorMessages::DB_QUERY_ERR], null, $th->getMessage());
    }

    return $res;
}
