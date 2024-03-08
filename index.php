<?php 

// include kernel
require_once 'core/kernel/kernel.php';

// include client config
require_once 'core/config.php';

// include request and response
require_once 'core/http/request.php';
require_once 'core/http/response.php';

// include error handler & notifier
require_once 'core/kernel/error_notifier.php';
require_once 'core/kernel/error_handler.php';

// include router
require_once 'core/kernel/router.php';

// include composer autoloader
require_once 'vendor/autoload.php';
// include custom classes autoloader
require_once 'core/classes/autoload.php';

// include enum files
foreach (glob(ROOT_DIR . 'core/enums/*.php') as $filename) { require_once $filename; }
// include locale files
foreach (glob(ROOT_DIR . 'core/locales/*.php') as $filename) { require_once $filename; }
// include all function files
foreach (glob(ROOT_DIR . 'core/functions/*.php') as $filename) { require_once $filename; }

// include MySQL connection
require_once 'core/db/mysql/conn.php';
// include Redis connection
require_once 'core/db/redis/conn.php';

// route to api file
require_once $_ROUTER['target_dir'];
