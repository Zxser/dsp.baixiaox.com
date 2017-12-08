<?php
function FN_md5_enctype($num = FALSE) {
	$num = $num ? $num : 8;
	$str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%&*+-=';
	$tmp = '';
	for ($i = 0; $i < $num; $i++) {
		$tmp .= $str[mt_rand(0, intval(strlen($str) - 1))];
	}
	return $tmp;
}

function FN_md5_password($passwd, $enctype) {
	return $passwd && $enctype ? md5($passwd . $enctype) : '';
}

function FN_md5_password_verify($passwd, $md5_passwd, $enctype) {
	return FN_md5_Password($passwd, $enctype) == $md5_passwd ? true : false;
}

function FN_Hash_password_hash($passwd) {
	return $passwd ? password_hash($passwd, PASSWORD_DEFAULT) : null;
}

function FN_Hash_password_verify($passwd, $hash) {
	return password_verify($passwd, $hash) ? true : false;
}

function FN_Hash_password_get_info($hash) {
	return isset($hash) || !empty($hash) ? password_get_info($hash) : null;
}

function FN_generator_id() {
	$id = '';
	$num = '123456789';
	list($usec, $sec) = explode(' ', microtime());
	$timer = ($usec + $sec) * 10000;
	$tmp = '';
	for ($i = 0; $i < (16 - (strlen($timer))); $i++) {
		$tmp .= $num[mt_rand(0, strlen($num) - 1)];
	}
	return $timer . $tmp;
}


function FN_GET_CLIENT_IP(){  
    global $ip;  
    if (getenv("HTTP_CLIENT_IP"))  
        $ip = getenv("HTTP_CLIENT_IP");  
    else if(getenv("HTTP_X_FORWARDED_FOR"))  
        $ip = getenv("HTTP_X_FORWARDED_FOR");  
    else if(getenv("REMOTE_ADDR"))  
        $ip = getenv("REMOTE_ADDR");  
    else $ip = "Unknow";  
    return $ip;  
}  