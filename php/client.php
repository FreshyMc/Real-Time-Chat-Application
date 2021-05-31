<?php
error_reporting(E_WARNING);

set_time_limit(0);

date_default_timezone_set('Europe/Sofia');

$log_file = 'log.txt';
$messages_file = 'messages.txt';
$buff_size = 1024;

$log_handler = fopen($log_file, 'r+') or die("Could not open/create log file.\n");

fseek($log_handler, 0);

$last = fgets($log_handler);

if(!$last)
    $last = round(microtime(true) * 1000);

$messages_handler = fopen($messages_file, 'r') or die("Could not open/create messages file.\n");

$current = $_SERVER['REQUEST_TIME'];

//TODO Replace with header function
header("Content-Type: application/json");
header("Cache-Control: no-cache");
header("Content-Type: text/html");
header("Connection: keep-alive");
header("Keep-Alive: timeout=5, max=1000");
flush();

do{
    usleep(500);
    clearstatcache();
    $last_timestamp = fileatime($messages_file);
}while($current >= $last_timestamp);

//Message loop
$time_wrapper = 0;

do {
    $line = trim(fgets($messages_handler));
	
	$decoded = json_decode($line, true);

    if($last <= $decoded['t']){
        $time_wrapper = $decoded['t'];
        echo $line . "\r\n";
    }
} while (! feof($messages_handler));

//Save timestamp of the last message
fseek($log_handler, 0);
fwrite($log_handler, $time_wrapper + 1);

//End operations
fclose($messages_handler);
fclose($log_handler);
