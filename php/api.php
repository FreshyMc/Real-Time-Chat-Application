<?php
date_default_timezone_set('Europe/Sofia');

$request_method = $_SERVER["REQUEST_METHOD"];
$request_time = date("h:i:sa", $_SERVER["REQUEST_TIME"]);
$request_path = $_SERVER["REQUEST_URI"];
$resource_path = '/chat/php/api.php';
$position = strrpos($request_path, $resource_path);
$actual_path = substr($request_path, $position + strlen($resource_path));

$headers = getallheaders();

$endpoints = array('/chat_enter', '/chat_room', '/contact');

$query_string = substr($actual_path, strpos($actual_path, '?') + 1);

switch ($request_method) {
    case 'POST':
        print_r($request_path);
        print_r($actual_path);
        break;
    case 'GET':
        echo $request_path . '<br>';
        echo $actual_path . '<br>';
        echo $query_string . '<br>';
        break;
    case 'PUT':
        print_r($request_path);
        break;
    case 'DELETE':
        print_r($request_path);
        break;
    default:
        header("HTTP/1.0 405 Method Not Allowed");
}

exit;