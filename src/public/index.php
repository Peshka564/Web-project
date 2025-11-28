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
        use Greeter\Greeter;
        use Foo\Bar\Baz;

        $greeter = new Greeter();
        $baz = new Baz();


        echo $greeter->hello();
        echo "<br>";
        echo $baz->hello();

        phpinfo();
    ?>
</body>
</html>