<?php

// url parts
$_ROUTER['url_parts'] =  array_filter(explode('/', $_REQUEST['request_uri']), function($item) {
    return ((strlen($item) == 0 || in_array($item, ['..', '.', '~'])) ? null : $item);
});

// url dynamic parameters
$_ROUTER['url_parameters'] = [];

// api search directory
$_ROUTER['target_dir'] = ROOT_DIR . 'api/' . API_VERSION . '/';

// dynamically search route
foreach ($_ROUTER['url_parts'] as $part) {
    // search dynamic part using regex
    $foundByRegex = false;
    foreach (DYMANIC_ROUTES_REGEX as $key => $regex) {
        if (preg_match($regex, $part) === 1) {
            $_ROUTER['url_parameters'][] = [$key => $part];
            $_ROUTER['target_dir'] .= $key . '/';
            $foundByRegex = true;
            break;
        }
    }

    // if exists by uri part
    if (file_exists($_ROUTER['target_dir'] . $part . '/')) {
        $_ROUTER['target_dir'] .= $part . '/';
        continue;
    }
    // if exists by dynamic route
    else if ($foundByRegex) {
        continue;
    }
    // if not found by any possible way
    else {
        response(404);
    }
}

// allocate folder in path by request method
if (file_exists($_ROUTER['target_dir'] . "[".strtolower($_REQUEST['request_method'])."]")) {
    $_ROUTER['target_dir'] .= "[".strtolower($_REQUEST['request_method']) . ']/';
}
// if not present 404 with debug error
else {
    response(404, null, null, 'URL method folder in API path not found');
}

// add filename
$_ROUTER['target_dir'] .= 'index.php';

// validate path existance
if (!realpath($_ROUTER['target_dir'])) {
    response(404);
}
