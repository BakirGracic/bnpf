<?php

function generateUUIDv4() {
    $data = random_bytes(16);
    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}

function base64urlEncode($text) {
	return str_replace(["+", "/", "="], ["-", "_", ""], base64_encode($text));
}

function base64urlDecode($text) {
	return base64_decode(str_replace(["-", "_"], ["+", "/"], $text));
}
