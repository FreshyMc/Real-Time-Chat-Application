<?php
error_reporting(E_WARNING);

set_time_limit(0);

date_default_timezone_set('Europe/Sofia');

$host = '127.0.0.1';
$port = 8088;
$max_connections = 4;
$welcome_msg = "Welcome to our server!\r\n";
$log_file = 'log.txt';
$messages_file = 'messages.txt';
$read_buffer_size = 1024;

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");

socket_listen($socket);

echo "Server started!\r\n";

while (true) {
    $client = socket_accept($socket);

    if ($client != false) {
        $log_handler = fopen($log_file, 'w');

        $messages_handler = fopen($messages_file, 'a');

        $buff = socket_read($client, $read_buffer_size);

        $t = time();

        fwrite($log_handler, $t, strlen($t)); 

        usleep(500);

        print_r($buff);

        $response = "HTTP/1.1 200 OK\r\n";
        $response .= "Access-Control-Allow-Origin: *\r\n";
        $response .= "Access-Control-Allow-Headers: *\r\n";
        $response .= "Allow: OPTIONS, GET, HEAD, POST\r\n";
        $response .= "Origin: http://" . $host . ":" . $port . "\r\n";
        $response .= "Content-Type: application/json\r\n";
        $response .= "Cache-Control: no-cache\r\n";
        $response .= "Connection: keep-alive\r\n";
        $response .= "Keep-Alive: timeout=5, max=1000\r\n";
        $response .= "\r\n\r\n";

        socket_write($client, $response, strlen($response));

        $headers = explode("\r\n", $buff);

        if ($headers[0] == 'POST / HTTP/1.1') {
            $content = $headers[count($headers) - 1];

            fwrite($messages_handler, $content . "\r\n");

            $messages = fopen($messages_file, 'r');

            $content = '';

            $headers = "HTTP/1.1 200 OK\r\n";
            $headers .= "Connection: close\r\n";
            $headers .= "Origin: http://" . $host . ":" . $port . "\r\n";
            $headers .= "Access-Control-Allow-Origin: *\r\n";
            $headers .= "Access-Control-Allow-Headers: *\r\n";
            $headers .= "Cache-Control: no-cache\r\n";
            $headers .= "Content-Type: application/json\r\n";
            $headers .= "Content-Length: " . strlen($content) . "\r\n";
            $headers .= "\r\n\r\n";
            $headers .= $content;

            socket_write($client, $headers, strlen($headers));
        }

        usleep(500);

        socket_close($client);

        fclose($messages_handler);
        fclose($log_handler);
    }
}
