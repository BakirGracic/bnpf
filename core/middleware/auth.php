<?php

// protected route
if (!is_bool($protectedRoute)) {
    throw new Exception('Route Option: $protectedRoute method must be true or false');
}
if ($protectedRoute) {
    authenticate();
}

// permissions
if (!is_null($routePermissions)) {
	if (!is_array($routePermissions)) {
        throw new Exception('Route Option: $routePermissions must be an array of required permissions as strings');
    }
	else {
        authenticate($routePermissions);
	}
}

// user auth data
$_AUTH = [];

// middleware functions
function authenticate() {
    global $_AUTH;

    // get access token from header
	$auth_header = $_REQUEST['user_auth_token'];

    // if token not privided
	if ($auth_header['type'] !== 'Bearer' || is_null($auth_header['token'])) {
		response(401);
	}

    // set token in variable
    $jwt = $auth_header['token'];

    // validate token and get payload
	$payload = decodeJWT($jwt);
	if ($payload === false) {
		response(401);
	}

    // check if token banned
    $isBanned = runRedis('get', ["banned_access_token:$jwt"]);
	if ($isBanned !== false) {
		response(401);
	}

    // get auth data
    $authData = runRedis('get', ["auth:$payload[id]"]);
    // auth data found
    if ($authData !== false) {
        $_AUTH = json_decode($authData);
	}
    // auth data NOT found -> get from MySQL and store
    else {
        // TODO finish auth

        // get mysql data

        // if there is NO data

        // make array and encode it

        // save to redis

        // save to $_AUTH variable
    }
}
function authorize($perms) {
    global $_AUTH;

    foreach ($perms as $item) {
        if (!in_array($item, $_AUTH['permissions'])) {
            response(403);
        }
    }
}
