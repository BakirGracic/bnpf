<?php

function handleError($code, $msg, $file, $line) {
	global $_REQUEST, $_KERNEL;

	// if error suppressed with @ --> then skip
	if (error_reporting() != E_ALL) {
		return false;
	}

	$error_id = uniqid();
	
	$error_info = [
		'id' => $error_id,
		'type' => 'error',
		'code' => $code,
		'severity' => ERROR_LEVELS[$code],
		'message' => $msg,
		'file' => $file,
		'line' => $line,
        'request_uri' => $_REQUEST['request_uri'],
        'request_method' => $_REQUEST['request_method'],
        'request_api' => (isset($_ROUTER['target_dir']) ? $_ROUTER['target_dir'] : null),
        'execution_time' => $_KERNEL['execution_time'],
        'debug' => $_REQUEST['debug'],
	];

	logError($error_info);

	if ($_REQUEST['debug']) {
		response(500, $error_info);
	} else {
		notifyErrorTelegram("[bakirs-server] {api.plooxy.io} -> ERROR: $error_id");
		response(500, ['error_id' => $error_id]);
	}
}

function handleException(\Throwable $exception) {
	global $_REQUEST, $_KERNEL;

	$error_id = uniqid();

	$error_info = [
		'id' => $error_id,
		'type' => 'exception',
		'code' => $exception->getCode(),
		'message' => $exception->getMessage(),
		'file' => $exception->getFile(),
		'line' => $exception->getLine(),
		'trace_str' => $exception->getTraceAsString(),
		'trace' => $exception->getTrace(),
		'previous' => $exception->getPrevious(),
		'request_uri' => $_REQUEST['request_uri'],
        'request_method' => $_REQUEST['request_method'],
        'request_api' => (isset($_ROUTER['target_dir']) ? $_ROUTER['target_dir'] : null),
        'execution_time' => $_KERNEL['execution_time'],
        'debug' => $_REQUEST['debug'],
	];

	logError($error_info);

	if ($_REQUEST['debug']) {
		response(500, $error_info);
	} else {
		notifyErrorTelegram("[bakirs-server] {api.plooxy.io} -> EXCEPTION: $error_id");
		response(500, ['error_id' => $error_id]);
	}
}

set_error_handler('handleError');
set_exception_handler('handleException');
