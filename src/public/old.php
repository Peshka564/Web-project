<?php

require_once __DIR__ . '/../autoload.php';

use Emitters\JsonEmitter\JsonEmitter;
use JsonParser\Lexer\Lexer;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use JsonParser\Parser\Parser;

$input = '';
$transformer = '';
$result = '';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get values from textareas
    $input = $_POST['input'] ?? '';
    $transformer = $_POST['transformer'] ?? '';

    $ctx = new TransformerContext(__DIR__ . "/../lib/Transformer/std", __DIR__ . "/../plugins");
    $parserResult = new Parser(new Lexer($input, 4))->parse();

    if ($parserResult->isErr()) {
        $result = "Parsing input error";
    } else {
        $result = new JsonEmitter()->emit(Evaluator::eval($transformer, $parserResult->ok(), $ctx));
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <form method="post" action="">
        <label for="input">Json</label><br>
        <textarea name="input" id="input" rows="10"
            cols="100"><?php echo $input; ?></textarea><br><br>

        <label for="textarea2">Transformer</label><br>
        <textarea name="transformer" id="transformer" rows="10"
            cols="100"><?php echo $transformer; ?></textarea><br><br>

        <input type="submit" value="Convert">
    </form>

    <?php if ($result): ?>
        <div style="margin-top:20px; padding:10px; border:1px solid #ccc;">
            <?php echo $result; ?>
        </div>
    <?php endif; ?>
</body>

</html>