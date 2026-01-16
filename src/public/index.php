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
       
        $user = "admin";
        $pass = "admin";
        $options = [
                // throws exception of error
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // return map or assositive array which basicly map
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            $pdo = new PDO("mysql:host=db;dbname=demo", $user, $pass, $options);
            $stmt = $pdo->query("SELECT * FROM demo_table");
            echo "
                <table style=\"margin: auto;\">
                    <thead>
                        <tr>
                            <th>id</th>
                            <th>fullname</th>
                        </tr>
                    </thead>
                    <tbody>";

            foreach ($stmt as $row) {
                $id = $row["id"];
                $fullname = $row["fullname"];
                echo "
                        <tr>
                            <td>$id</td>
                            <td>$fullname</td>
                        </tr>
                ";
            }

            echo "
                    </tbody>
                </table>
            ";         

        } catch (Exception $e) {
            echo "Something have gone wrong -> " . $e->getMessage();
        }
      
        phpinfo();
    ?>
</body>
</html>