<?php

// execution time
$_JOB['execution_start'] = microtime(true);

// get necessary code
require_once __DIR__ . '/../core/config.php';
require_once __DIR__ . '/../core/kernel/error_notifier.php';
require_once __DIR__ . '/../core/db/mysql/conn.php';
require_once __DIR__ . '/../core/db/redis/conn.php';
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../core/classes/autoload.php';

// log completed job
function logJobCompleted() {
    global $_JOB;

    $data = [
        'time' => date('[d-M-Y H:i:s T]'),
        'job_id' => $_JOB['uuid'] ?? null,
        'job_type' => $_JOB['type'] ?? null,
        'execution_time' => round(((float)microtime(true) - (float)$_JOB['execution_start']) * 1000, 3) . "ms",
    ];

    $filename = date('d_m_y', time()) . '.json';
    file_put_contents(__DIR__ . "/logs/completed/" . $filename, json_encode($data) . PHP_EOL, FILE_APPEND | LOCK_EX);

    exit;
}

// log failed job
function logJobFailed($error) {
    global $_JOB;

    $data = [
        'time' => date('[d-M-Y H:i:s T]'),
        'job_id' => $_JOB['uuid'] ?? null,
        'job_type' => $_JOB['type'] ?? null,
        'execution_time' => round(((float)microtime(true) - (float)$_JOB['execution_start']) * 1000, 3) . "ms",
        'error' => $error
    ];

    $filename = date('d_m_y', time()) . '.json';
    file_put_contents(__DIR__ . "/logs/failed/" . $filename, json_encode($data) . PHP_EOL, FILE_APPEND | LOCK_EX);

    exit;
}

// get pending job with locking
try {
    $_MYSQL->begin_transaction();

    $result = $_MYSQL->query("SELECT * FROM jobs WHERE status = 'pending' LIMIT 1 FOR UPDATE SKIP LOCKED");

    // if no pending jobs, exit
    if ($result->num_rows == 0) {
        $_MYSQL->commit();
        logJobCompleted();
    }

    // add job details to array
    $_JOB = array_merge($result->fetch_assoc(), $_JOB);
    $_JOB['data'] = json_decode($_JOB['data'], true);

    // set job as processing
    $result = $_MYSQL->query("UPDATE jobs SET status = 'processing', processed_at = NOW() WHERE uuid = '{$_JOB['uuid']}'");

    // process job (include file)
    if (!file_exists(__DIR__ . "/tasks/{$_JOB['type']}.php")) {
        throw new Exception('Task file not found');
    } else {
        require_once __DIR__ . "/tasks/{$_JOB['type']}.php";
    }

    // set job as completed
    $result = $_MYSQL->query("UPDATE jobs SET status = 'completed', completed_at = NOW()  WHERE uuid = '{$_JOB['uuid']}'");

    $_MYSQL->commit();
} catch (\Throwable $th) {
    $_MYSQL->rollback();
    // set job status as failed
    $result = $_MYSQL->query("UPDATE jobs SET status = 'failed', processed_at = NULL, completed_at = NULL WHERE uuid = '{$_JOB['uuid']}'");
    // notify error
    notifyErrorTelegram("[bakirs-server] {api.plooxy.io} -> JOB ERROR: ".$_JOB['uuid'] ?? 'unknown_job_id');
    // log job failed
    logJobFailed($th->getMessage());
}

// log job completion
logJobCompleted();
