<?php
//DB Client credentials
define('DB_NAME', 'chat');
define('DB_PASSWORD', '');
//Hosting parameters
define('DB_USERNAME', 'root');
define('DB_HOSTNAME', 'localhost');
//SQL Statements
define('LOGON', 'SELECT `password`, `token`, `username` FROM `users` WHERE `username` = ? LIMIT 1;');
define('REGISTER', 'INSERT INTO `users`(`username`, `password`, `token`) VALUES (?, ?, ?);');