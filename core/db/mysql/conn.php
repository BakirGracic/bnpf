<?php

try {
	$_MYSQL = new \mysqli(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
	if ($_MYSQL->connect_error) {
		response(500, ['error' => DebugErrorMessages::DB_CONN_ERR], null, $_MYSQL->connect_error);
	}
} catch (Exception $e) {
	response(500, ['error' => DebugErrorMessages::DB_CONN_ERR], null, $e->getMessage());
}

$_MYSQL->set_charset('utf8mb4');
