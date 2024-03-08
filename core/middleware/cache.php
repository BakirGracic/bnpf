<?php

// cache response
if (!is_bool($cacheResponse)) {
	throw new Exception('Route Option: $cacheResponse must be true or false');
}
if ($_REQUEST['request_method'] == "POST" || $_REQUEST['request_method'] == "DELETE") {
	$cacheResponse = false;
}
if ($cacheResponse == false) {
	$_RESPONSE['send_headers'][] = 'Cache-Control: no-cache, must-revalidate, max-age=0';
}
else {
	$_RESPONSE['send_headers'][] = 'Cache-Control: max-age=' . API_DEFAULT_REVALIDATE_CACHE;
	// TODO make advanced API response caching with route dependency checking 
}
