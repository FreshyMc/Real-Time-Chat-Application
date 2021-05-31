<?php
date_default_timezone_set('Europe/Sofia');

$file_path = 'messages.txt';

$file_handler = fopen('messages.txt', 'r');

$search_token = '@6970eefb8bde928604b9';

$current = $_SERVER['REQUEST_TIME'];

echo $current . '<br>';

$file = NULL;

do {
    $line = trim(fgets($file_handler));
	
	$decoded = json_decode($line, true);

    if($decoded['type']==1){
        continue;
    }
    
    if($decoded['file_token']==$search_token){
        $file = $decoded;
        break;
    }
} while (! feof($file_handler));

fclose($file_handler);

if($file != NULL){
    echo json_encode($file);
    exit;
}