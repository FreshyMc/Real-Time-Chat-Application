<?php
if (isset($_POST['file']) && !empty($_POST['file'])) {
    $file_message = json_decode($_POST['file'], true);

    $messages_file = 'messages.txt';

    $file_token = '@' . bin2hex(random_bytes(10));

    $file_message['file_token']=$file_token;

    $result = json_encode($file_message);

    $messages_handler = fopen($messages_file, 'a');

    fwrite($messages_handler, $result . "\r\n");

    fclose($messages_handler);

    echo 'OK';
    exit;
}
