<?php

function encodeJWT($payload) {
    if (!is_array($payload)) throw new Exception('Payload not an array');
    if (!isset($payload['id']) || empty($payload['id'])) throw new Exception('Missing ID claim in payload');
    if (!isset($payload['exp']) || empty($payload['exp'])) throw new Exception('Missing EXP claim in payload');
    if (!isset($payload['iss']) || empty($payload['iss'])) throw new Exception('Missing ISS claim in payload');
    
    $header = json_encode(["alg" => "HS256", "typ" => "JWT"]);
    $header = base64urlEncode($header);
    
    $payload = json_encode($payload);
    $payload = base64urlEncode($payload);
    
    $signature = hash_hmac("sha256", $header . "." . $payload, ACCESS_TOKEN_SECRET, true);
    $signature = base64urlEncode($signature);
    
    return $header . "." . $payload . "." . $signature;
}

function decodeJWT($token) {
    global $_KERNEL;
        
    if (!preg_match("/^(?<header>.+)\.(?<payload>.+)\.(?<signature>.+)$/", $token, $parts)) {
        return false;
    }
    
    $signature_token = base64urlDecode($parts["signature"]);
    $signature = hash_hmac("sha256", $parts["header"] . '.' . $parts["payload"], ACCESS_TOKEN_SECRET, true);
    if (!hash_equals($signature, $signature_token)) {
        return false;
    }
    
    $payload = json_decode(base64urlDecode($parts["payload"]), true);
    
    if (!is_array($payload)) {
        return false;
    }
    
    if ($payload['exp'] < (int)$_KERNEL['datetime']->getTimestamp()) {
        return false;
    }
    
    return $payload;
}
