<?php

// request timeout
if ($requestTimeout !== API_DEFAULT_TIMEOUT) {
	if (is_int($requestTimeout) && $requestTimeout > 10000) {
		set_time_limit($requestTimeout);
	}
	else {
		throw new Exception('Route Option: $requestTimeout value must be an integer bigger than 10000');
	}
}

// rate limit
if ($requestRateLimit !== API_DEFAULT_RATE_LIMIT) {
	if (!is_string($requestRateLimit) || preg_match("/^\d+\|\d+$/", $requestRateLimitteLimit)) {
		throw new Exception('Route Option: $requestRateLimit must be a string of format REQUESTS_NUMBER|WINDOW_SECONDS_NUMBER');
	}
	else {
		rateLimit($requestRateLimit);
	}
} else {
	rateLimit(API_DEFAULT_RATE_LIMIT);
}

// middleware functions
function rateLimit($requestRateLimit) {
	global $_REQUEST, $_RESPONSE;

	// disable rate limiting for debug requests
	if ($_REQUEST['debug']) { return; }

	// get user browser fingerprint
	$browser_fingerprint = null;
	if (is_null($_REQUEST['user_browser_fingerprint'])) {
		$data = [
			'user_agent' => $_REQUEST['user_agent'],
			'user_ip' => $_REQUEST['user_ip'],
		];
		$browser_fingerprint = hash('sha256', serialize($data));
	} else {
		$browser_fingerprint = $_REQUEST['user_browser_fingerprint'];
	}

	// redis prepare 
	$redis_key = "rate_limit:$browser_fingerprint";
	$limit = (int)(explode('|', $requestRateLimit)[0]);
	$window = (int)(explode('|', $requestRateLimit)[1]);

	// prepare rate limit header
	$_RESPONSE['send_headers'][] = "X-RateLimit-Limit: $limit";

	// get redis data for rate limiting
	$hits = runRedis('get', [$redis_key]);

	// if has recent hits
	if ($hits !== false) {
		$left = runRedis('ttl', [$redis_key]);
		// if above limit
		if ($hits >= $limit) {
			response(429, null, [
				"X-RateLimit-Remaining: 0",
				"X-RateLimit-Reset: " . ((int)$_REQUEST['user_timestamp'] + $left),
				"Retry-After: $left",
			]);
		}
		// if below limit
		else {
			runRedis('incr', [$redis_key]);
			$_SEND_HEADERS[] = "X-RateLimit-Remaining: " . (int)$limit - (int)$hits;
			$_SEND_HEADERS[] = "X-RateLimit-Reset: " . ((int)$_REQUEST['user_timestamp'] + $left);
		}
	}
	// if no recent hits
	else {
		runRedis('set', [$redis_key, 1]);
		runRedis('expire', [$redis_key, $window]);
		$_SEND_HEADERS[] = "X-RateLimit-Remaining: " . (int)$limit - 1;
		$_SEND_HEADERS[] = "X-RateLimit-Reset: " . ((int)$_REQUEST['user_timestamp'] + $window);
	}
}
