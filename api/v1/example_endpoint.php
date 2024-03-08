<?php

// route options
$requestTimeout   = API_DEFAULT_TIMEOUT;
$requestRateLimit = API_DEFAULT_RATE_LIMIT;
$protectedRoute   = false;
$routePermissions = null;
$cacheResponse    = false;

// process route options
require_once ROOT_DIR . 'core/middleware/request.php';
require_once ROOT_DIR . 'core/middleware/auth.php';
require_once ROOT_DIR . 'core/middleware/cache.php';

// -----------------------------

response(200, [
    'version' => API_VERSION,
    'service' => '_________________',
    'url' => API_URL,
    'utc_timestamp' => $_KERNEL['datetime']->getTimestamp(),
    'local_timestamp' => $_REQUEST['user_timestamp'],
]);

validateBody([
    'required' => [
        'first_name' => ['isAlpha|isFirstName|isAfrikati', '3|30'],
        'last_name' => ['isAlpha|isFirstName|isAfrikati', '3'],
        'username' => ['isAlpha|isFirstName|isAfrikati'],
        'email' => ['isEmail'],
        'parameters' => ['isArray'],
        'parameters.something' => ['isAlpha|isFirstName|isAfrikati', '3|30'],
    ],
    'optional' => [
        // can be the same as in required
    ],
]);

$_MYSQL->begin_transaction();
$_MYSQL->rollback();
$_MYSQL->commit();

$sql = [
    'SELECT * FROM database WHERE id = 1',
    'INSERT INTO database (id, name) VALUES (1, "John")',
    'UPDATE database SET name = "John" WHERE id = 1',
    'DELETE FROM database WHERE id = 1'
];

$options = [
    'cache' => true,
    'cache_ttl' => 3600, // 3600s / 1h
    'silence_duplicate_entry' => true,
    'silence_empty_select' => true,
];

$res = runSQL($sql, $options);
