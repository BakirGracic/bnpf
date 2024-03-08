<?php

// add global headers to send_headers queue
$_RESPONSE['send_headers'] = [
	'Access-Control-Allow-Origin: *',
	'Access-Control-Allow-Headers: *',
	'Access-Control-Allow-Methods: OPTIONS, GET, POST, DELETE',
	'Content-Type: application/json; charset=utf-8',
	'Accept: application/json',
	'X-Frame-Options: DENY',
	'X-Robots-Tag: noindex, nofollow',
];

// main response function
function response($code = 200, $body = null, $headers = null, $debug = null)
{
	global $_RESPONSE, $_KERNEL;

	// api execution time
	$_KERNEL['execution_stop'] = microtime(true);
	$_KERNEL['execution_time'] = round(($_KERNEL['execution_stop'] - $_KERNEL['execution_start']) * 1000, 3);
	if ($_REQUEST['debug']) $_RESPONSE['send_headers'][] = "API-Execution-Time: {$_KERNEL['execution_time']}ms";

	// headers buffer prepare
	if (!is_null($headers)) {
		foreach ($headers as $item) {
			$_RESPONSE['send_headers'][] = $item;
		}
	}

	// http code send
	http_response_code($code);

	// body send
	if (!is_null($body)) {
		echo json_encode($body, JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}
	// body send (debug)
	if (!is_null($debug) && $_REQUEST['debug']) {
		echo PHP_EOL . json_encode(['debug' => $debug], JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
	}

	// headers send
	foreach ($_RESPONSE['send_headers'] as $item) {
		header($item);
	}

	// close database connections if present
	if (isset($_MYSQL) && !empty($_MYSQL)) $_MYSQL->close();
	if (isset($_REDIS) && !empty($_REDIS)) $_REDIS->close();

	// log api
	logApi();

	exit;
}

// handle options/preflight request
if ($_REQUEST['server']['REQUEST_METHOD'] === 'OPTIONS') {
	response();
}
