<?php
require_once __DIR__ . '/../autoload.php';
session_start();

// Use your service to clean up DB and Session
$db = new db\DBClient();
$sessions = new db\repository\SessionRepository($db);
$users = new db\repository\UserRepository($db);
$auth = new services\AuthService($sessions, $users);

$auth->logoutUser();

header('Location: login.php');
exit;