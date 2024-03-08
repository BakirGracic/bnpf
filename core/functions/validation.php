<?php

function validateBody($config) {
    global $_REQUEST;

    $errors = [];

    // assign required and optional config to variables
    $required = (isset($config['required']) && !empty($config['required']) ? $config['required'] : null);
    $optional = (isset($config['optional']) && !empty($config['optional']) ? $config['optional'] : null);

    // validate required and optional arrays
    if (!is_array($required) && !is_null($required)) throw new Exception("Field Validator: 'required' must be an array");
    if (!is_array($optional) && !is_null($optional)) throw new Exception("Field Validator: 'optional' must be an array");

    // handling required
    if (!is_null($required)) {
        foreach ($required as $key => $rules) {
            $value = array_reduce(explode('.', $key), function ($carry, $item) {
                return isset($carry[$item]) ? $carry[$item] : null;
            }, $_REQUEST['body']);

            runValidations($key, $value, $rules, $errors);
        }
    }

    // handling optional
    if (!is_null($optional)) {
        foreach ($optional as $key => $rules) {
            $value = array_reduce(explode('.', $key), function ($carry, $item) {
                return isset($carry[$item]) ? $carry[$item] : null;
            }, $_REQUEST['body']);

            runValidations($key, $value, $rules, $errors);
        }
    }

    // removing extraneous
    foreach ($_REQUEST['body'] as $key => $value) {
        if (!isset($config['required'][$key]) && !isset($config['optional'][$key])) {
            unset($_REQUEST['body'][$key]);
        }
    }

    // if errors
    if (!empty($errors)) {
        response(400, ['validation_errors' => $errors]);
    }
}

function runValidations($key, $value, $rules, &$errors = []) {
    // get rules parts
    $type = $rules[0] ?? throw new Exception("Field Validator: At least a validation function must be set for key rule");
    $length = $rules[1] ?? null;

    // get type functions & call them
    $type_functions = explode('|', $type);
    foreach ($type_functions as $function) {
        if (is_callable($function)) {
            if (!call_user_func($function, $value)) {
                $errors[] = "{$key} is not " . strtolower(substr($function, 2));
            }
        }
        else throw new Exception("Field Validator: Unknown validation method '{$function}'");
    }

    // get length sizes & valiadate them
    if (!is_null($length)) {
        $length_sizes = explode('|', $length);
        $length_min = $length_sizes[0] ?? null;
        $length_max = $length_sizes[1] ?? null;
        if (!lengthCheck($value, $length_min, $length_max)) {
            $errors[] = "Length or size of '{$key}' invalid";
        }
    }
    
}

function lengthCheck($value, $min = null, $max = null) {
    $type = gettype($value);

    if ($type == 'integer' || $type == 'double' || $type == 'float') {
        if (!is_null($min) && $value < $min) { return false; }
        if (!is_null($max) && $value > $max) { return false; }
    }

    if ($type == 'string') {
        $strlen = strlen($value);
        if (!is_null($min) && $strlen < $min) { return false; }
        if (!is_null($max) && $strlen > $max) { return false; }
    }

    if ($type == 'array') {
        $count = count($value);
        if (!is_null($min) && $count < $min) { return false; }
        if (!is_null($max) && $count > $max) { return false; }
    }

    return true;
}



// custom validation functions

// data types
function isInteger($value) {
	return filter_var($value, FILTER_VALIDATE_INT) !== false;
}
function isFloat($value) {
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
}
function isString($value) {
    return is_string($value);
}
function isArray($value) {
    return is_array($value);
}
function isObject($value) {
    return is_object($value);
}
function isBoolean($value) {
    return is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
}

// string types
function isAlpha($value) {
    return preg_match('/^\p{L}+$/u', (string)$value) === 1;
}
function isAlphanumeric($value) {
	return preg_match('/^[\p{L}\p{N}]+$/u', (string)$value) === 1;
}
function isEmail($value) {
    return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
}
function isGUIDv4($value) {
    return is_string($value) && preg_match("/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i", $value) === 1;
}
function isPassword($value) {
    return strlen($value) >= 8 &&
           preg_match("#[0-9]+#", $value) &&
           preg_match("#[A-Z]+#", $value) &&
           preg_match("#[a-z]+#", $value) &&
           preg_match("#[\W]+#", $value);
}
function isURL($value) {
    return filter_var($value, FILTER_VALIDATE_URL) !== false;
}
function isIP($value) {
    return filter_var($value, FILTER_VALIDATE_IP) !== false;
}
function isCredit_Card($value) {
    return preg_match("/^\d{4}-\d{4}-\d{4}-\d{4}$/", (string)$value) === 1;
}

// int types
function isUNIX_Timestamp($value) {
    return preg_match("/^\d{10}$/", (string)$value) === 1;
}
function isPhone($value) {
    return preg_match("/^\+?\d{10,14}$/", (string)$value) === 1;
}
function isBinary($value) {
    return preg_match("/^[01]+$/", (string)$value) === 1;
}

// complex types
function isJson($value) {
    json_decode($value, true);
    return (json_last_error() === JSON_ERROR_NONE);
}
