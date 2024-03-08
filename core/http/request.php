<?php

// GET
$_REQUEST['get'] = $_GET;
foreach ($_REQUEST['get'] as $key => $value) {
    $_REQUEST['get'][$key] = htmlspecialchars(urldecode(trim($value)));
}

// COOKIE
$_REQUEST['cookie'] = $_COOKIE;

// SERVER
$_REQUEST['server'] = $_SERVER;

// BODY
function getBody()
{
    $data = json_decode(file_get_contents('php://input'), true);

    if ($data === null || json_last_error() !== JSON_ERROR_NONE) {
        return [];
    }

    array_walk_recursive($data, function (&$item) {
        if (is_string($item)) {
            $item = htmlspecialchars(trim($item));
        }
    });

    return $data;
}
$_REQUEST['body'] = getBody();

// HEADERS
function retrieve_headers()
{
    $headers = [];

    if (function_exists('getallheaders')) {
        $headers = getallheaders();
    } else {
        foreach ($_REQUEST['server'] as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
    }

    return $headers;
}
$_REQUEST['headers'] = retrieve_headers();


// request uri
$_REQUEST['request_uri'] = htmlspecialchars(trim(explode('?', $_REQUEST['server']['REQUEST_URI'], 2)[0]), ENT_QUOTES, 'UTF-8');

// request mehthod
$_REQUEST['request_method'] = $_REQUEST['server']['REQUEST_METHOD'];

// debug
$_REQUEST['debug'] = ((isset($_REQUEST['get']['debug']) && !empty($_REQUEST['get']['debug']) && $_REQUEST['get']['debug'] == DEBUG_TOKEN) ? true : false);

// user ip
function getUserIp()
{
    $ip_keys = [
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    ];

    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_REQUEST['server']) === true) {
            foreach (explode(',', $_REQUEST['server'][$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var(
                    $ip,
                    FILTER_VALIDATE_IP,
                    FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6 |
                        FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
                ) !== false) {
                    return $ip;
                }
            }
        }
    }

    return null;
}
$_REQUEST['user_ip'] = getUserIp();

// user agent
function getUserAgent()
{
    if (!isset($_REQUEST['headers']['User-Agent'])) {
        return null;
    }

    $user_agent = $_REQUEST['headers']['User-Agent'];

    $user_agent = substr(trim($user_agent), 0, 255);

    return $user_agent;
}
$_REQUEST['user_agent'] = getUserAgent();

// user browser fingerprint
function getUserBrowserFingerprint()
{
    if (!isset($_REQUEST['headers']['User-Browser-Fingerprint'])) {
        return null;
    }

    return htmlspecialchars(trim($_REQUEST['headers']['User-Browser-Fingerprint']));
}
$_REQUEST['user_browser_fingerprint'] = getUserBrowserFingerprint();

// user auth token
function getAuthToken()
{
    if (!isset($_REQUEST['headers']['Authorization'])) {
        return null;
    } else {
        $parts = explode(" ", htmlspecialchars(trim($_REQUEST['headers']['Authorization'])));
        return [
            'type' => $parts[0] ?? null,
            'token' => $parts[1] ?? null,
        ];
    }
}
$_REQUEST['user_auth_token'] = getAuthToken();

// user geo
function getUserGeo()
{
    global $_REQUEST;

    return [
        'country'      => $_REQUEST['headers']['X-Geo-Country'] ?? null,
        'country_code' => $_REQUEST['headers']['X-Geo-Country_Code'] ?? null,
        'region'       => $_REQUEST['headers']['X-Geo-Region'] ?? null,
        'region_name'  => $_REQUEST['headers']['X-Geo-Region_Name'] ?? null,
        'city'         => $_REQUEST['headers']['X-Geo-City'] ?? null,
        'zip'          => $_REQUEST['headers']['X-Geo-ZIP'] ?? null,
        'latitude'     => $_REQUEST['headers']['X-Geo-Latitude'] ?? null,
        'longitude'    => $_REQUEST['headers']['X-Geo-Longitute'] ?? null,
        'timezone'     => $_REQUEST['headers']['X-Geo-Timezone'] ?? null,
    ];
}
$_REQUEST['user_geo'] = getUserGeo();

// local timestamp
function userTimestamp()
{
    global $_KERNEL, $_REQUEST;

    $datetime = $_KERNEL['datetime'];

    if (is_null($_REQUEST['user_geo']['timezone'])) {
        return $datetime->getTimestamp();
    }

    $datetime->setTimezone(new DateTimeZone($_REQUEST['user_geo']['timezone']));
    $offset = $datetime->getOffset();

    return $datetime->getTimestamp() + $offset;
}
$_REQUEST['user_timestamp'] = userTimestamp();

// user client data array
function getClientData() {
    return [
        'ip' => $_REQUEST['user_ip'],
        'agent' => $_REQUEST['user_agent'],
        'browser_fingerprint' => $_REQUEST['user_browser_fingerprint'],
        'geo' => $_REQUEST['user_geo'],
        'timestamp' => $_REQUEST['user_timestamp'],
    ];
}
$_REQUEST['client_data'] = getClientData();

// pagination
$_REQUEST['pagination_limit'] = ((isset($_REQUEST['get']['limit']) && is_int((int)$_REQUEST['get']['limit']) && (int)$_REQUEST['get']['limit'] > 0) ? (int)$_REQUEST['get']['limit'] : DEFAULT_PAGINATION_RESULTS);
$_REQUEST['pagination_page'] = ((isset($_REQUEST['get']['page']) && is_int((int)$_REQUEST['get']['page']) && (int)$_REQUEST['get']['page'] > 0) ? (int)$_REQUEST['get']['page'] : 1);
$_REQUEST['pagination_offset'] = (($_REQUEST['pagination_page'] > 1) ? (($_REQUEST['pagination_page'] * $_REQUEST['pagination_limit']) - 1) : 0);
