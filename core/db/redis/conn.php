<?php

try {
	$_REDIS = new Redis();
	$_REDIS->connect(REDIS_HOST, REDIS_PORT);
	$_REDIS->auth(REDIS_PASSWORD);
} catch (\Throwable $th) {
	response(500, ['error' => DebugErrorMessages::DB_CONN_ERR], null, $th->getMessage());
}

return $_REDIS;
