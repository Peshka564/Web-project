<?php
require_once __DIR__ . '/../autoload.php';
use db\repository\SessionRepository;
use db\repository\UserRepository;
use db\DBClient;
use services\AuthService;

session_start();

$db = new DBClient();
$sessions = new SessionRepository($db);
$users = new UserRepository($db);
$auth = new AuthService($sessions, $users);

$auth->guard();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSONConverter</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>
    <body class="flex-body">
        <nav class="sidebar">
            <div class="avatar-placeholder"><img src="img/avatar.png" class="avatar"></div> 
            <a href="converter.php" class="nav-item">Converter</a>
            <a href="logout.php" class="nav-item">Logout</a>
        </nav>

        <main class="main-content">
            <header class="top-bar">
                <h1>History</h1>
            </header>

            <section class="history-section">
                <article class="history-item">
                    <header class="history-item-header">
                        <h3 class="history-item-name">Name</h3>
                        <time class="history-item-date">Date</time>
                    </header>
                    <p class="history-item-description">Dummy data</p>
                </article>
            </section>


        </main>
    </body>
</html>