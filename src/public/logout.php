<?php

use db\DBClient;
require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config.php';

session_start();


$db = new DBClient(DB_CLIENT_HOST, DB_CLIENT_PORT, DB_CLIENT_DBNAME, DB_CLIENT_USER, DB_CLIENT_PASS);
$sessions = new db\repository\SessionRepository($db);
$users = new db\repository\UserRepository($db);
$auth = new services\AuthService($sessions, $users);

$auth->logoutUser();

header('Location: login.php');
exit;