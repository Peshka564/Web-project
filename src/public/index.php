<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php
        require_once __DIR__ . '/../autoload.php';
        use db\DBClient;
        use db\models\User;
        use db\repository\UserRepository;

        $dbClient = new DBClient();
        $repo = new UserRepository($dbClient);

        // Create User
        $user = $repo->create(new User('pesho@gmail.com', '12345'));
        echo $user->id;

        // Get All Users
        $users = $repo->findAll();

        // Get User by id
        $user = $repo->findById(1);
        if($user) {
            echo $user->email;
        }

        // Update User
        $new_user = $user;
        $new_user->email = 'pesho_new@gmail.com';
        $new_user = $repo->update($user->id, $new_user);
        echo $new_user->email;

        // Delete User
        $repo->delete($new_user->id);
    ?>
</body>
</html>