<?php

date_default_timezone_set('UTC');

define('ROOT_DIR', $_SERVER['DOCUMENT_ROOT']);

define('ERROR_LOG_DIR', ROOT_DIR . 'utils/logs/errors/');
define('API_LOG_DIR', ROOT_DIR . 'utils/logs/api/');

define('UPLOADS_DIR', ROOT_DIR . 'uploads/');

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('log_errors', 1);
define('ERROR_LEVELS', [
	E_WARNING => 'E_WARNING',
	E_NOTICE => 'E_NOTICE',
	E_USER_ERROR => 'E_USER_ERROR',
	E_USER_WARNING => 'E_USER_WARNING',
	E_USER_NOTICE => 'E_USER_NOTICE',
	E_RECOVERABLE_ERROR => 'E_RECOVERABLE_ERROR',
	E_DEPRECATED => 'E_DEPRECATED',
	E_USER_DEPRECATED => 'E_USER_DEPRECATED',
	E_ALL => 'E_ALL'
]);

if (php_sapi_name() === 'cli') {
	die("This script cannot be executed in command line");
}

$_KERNEL = [
	'execution_start' => microtime(true),
	'execution_stop' => null,
	'execution_time' => null,
	'datetime' => new DateTime(),
];

function logError($data) {
    global $_KERNEL;

    array_unshift($data, ['time' => date('[d-M-Y H:i:s T]', $_KERNEL['datetime']->getTimestamp())]);

    $filename = date('d_m_y', $_KERNEL['datetime']->getTimestamp()) . '.json';
    file_put_contents(ERROR_LOG_DIR . $filename, json_encode($data) . PHP_EOL, FILE_APPEND | LOCK_EX);
}

function logApi() {
    global $_KERNEL, $_REQUEST, $_ROUTER;

    $data = [
        'time' => date('[d-M-Y H:i:s T]', $_KERNEL['datetime']->getTimestamp()),
        'request_uri' => $_REQUEST['request_uri'],
        'request_method' => $_REQUEST['request_method'],
        'request_api' => (isset($_ROUTER['target_dir']) ? $_ROUTER['target_dir'] : null),
        'execution_time' => $_KERNEL['execution_time'],
        'debug' => $_REQUEST['debug'],
        'user_ip' => $_REQUEST['user_ip'],
        'user_agent' => $_REQUEST['user_agent'],
        'user_timestamp' => $_REQUEST['user_timestamp'],
    ];

    $filename = date('d_m_y', $_KERNEL['datetime']->getTimestamp()) . '.json';
    file_put_contents(API_LOG_DIR . $filename, json_encode($data) . PHP_EOL, FILE_APPEND | LOCK_EX);
}
