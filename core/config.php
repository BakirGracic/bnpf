<?php

// debug
define('DEBUG_TOKEN', '_________________');

// api
define('API_VERSION', 'v1');
define('API_URL', 'https://api.domain.com/');
define('API_DEFAULT_TIMEOUT', 10000); // 10000ms / 10s
define('API_DEFAULT_RATE_LIMIT', '120|60'); // requests_num|window_in_seconds
define('API_DEFAULT_REVALIDATE_CACHE', 21600); // 21600s / 6h

// dynamic routes regex
define('DYMANIC_ROUTES_REGEX', [
    ':id' => "/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i", // UUID v4 regex
]);

// pagiantion
define('DEFAULT_PAGINATION_RESULTS', 15);

// telegram error notifier
define('TELEGRAM_BOT_TOKEN', '_________________');
define('TELEGRAM_CHAT_ID', '_________________');

// mysql
define('MYSQL_HOST',     '_________________');
define('MYSQL_USER',     '_________________');
define('MYSQL_DATABASE', '_________________');
define('MYSQL_PASSWORD', '_________________');
define('MYSQL_QUERY_DEFAULT_CACHE_TTL', 3600); // 3600s / 1h

// redis
define('REDIS_HOST',     '_________________');
define('REDIS_PORT',     '_________________');
define('REDIS_PASSWORD', '_________________');

// auth tokens
define('ACCESS_TOKEN_SECRET', '_________________');
define('ACCESS_TOKEN_EXPIRE', 86400); // development 86400s / 1d, production 900s / 15m
define('REFRESH_TOKEN_SECRET', '_________________');
define('REFRESH_TOKEN_EXPIRE', 604800); // 604800s / 7d
define('REFRESH_PROLONG_WINDOW', 259200); // 259200s / 3d

// mail
define('MAIL_HOST',       '_________________');
define('MAIL_USERNAME',   '_________________');
define('MAIL_PASSWORD',   '_________________');
define('MAIL_PORT',       587);
define('MAIL_ENCRYPTION', '_________________');
define('MAIL_FROM',       '_________________');
define('MAIL_FROM_NAME',  '_________________');
