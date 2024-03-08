<?php

function runSQL($queries, $options)
{
    global $_MYSQL;

    $results = [];

    foreach ($queries as $query) {
        $cacheKey = null;
        if (!empty($options['cache']) && $options['cache'] === true && stripos(strtoupper($query), 'SELECT') === 0) {
            $cacheKey = 'mysql_query_cache:' . hash('sha256', $query);
            $cachedResult = runRedis('get', [$cacheKey]);
            if ($cachedResult !== false) {
                $results[] = json_decode($cachedResult, true);
                continue;
            }
        }

        try {
            $result = $_MYSQL->query($query);

            if ($cacheKey && $result !== false && $result instanceof mysqli_result) {
                runRedis('set', [$cacheKey, json_encode($result)], 'ex', $options['cache_ttl'] ?? MYSQL_QUERY_DEFAULT_CACHE_TTL);
            }

            $results[] = $result;
        } catch (\Throwable $th) {
            $_MYSQL->rollback();
            if ($th->getCode() == 1062) {
                if (!empty($options['silence_duplicate_entry']) && $options['silence_duplicate_entry'] === true) {
                    $results[] = ['error' => MySQLErrorCodes::DUPLICATE_ENTRY];
                } else {
                    response(400, ['error' => DebugErrorMessages::QUERY_DUPLICATE_ENTRY]);
                }
            } else {
                response(500, ['error' => DebugErrorMessages::DB_QUERY_ERR], null, $th->getMessage());
            }
        }
    }

    foreach ($results as $i => $result) {
        if (is_array($result) && isset($result['error'])) continue;

        if ($result === true) {
            $results[$i] = $_MYSQL->affected_rows;
        } elseif ($result instanceof mysqli_result) {
            if ($result->num_rows == 0) {
                if (!empty($options['silence_empty_select'])) {
                    $results[$i] = [];
                } else {
                    response(204);
                }
            } elseif ($result->num_rows == 1) {
                $results[$i] = decodeJSONRecursive($result->fetch_assoc());
            } else {
                $results[$i] = decodeJSONRecursive($result->fetch_all(MYSQLI_ASSOC));
            }
        } else {
            $results[$i] = decodeJSONRecursive($result);
        }
    }

    return count($results) == 1 ? $results[0] : $results;
}

function decodeJSONRecursive($data)
{
    if (is_array($data)) {
        foreach ($data as &$value) {
            $value = is_string($value) ? json_decode($value, true) ?? $value : decodeJSONRecursive($value);
        }
    }
    return $data;
}
