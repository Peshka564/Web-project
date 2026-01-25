<?php

require_once __DIR__ . '/../autoload.php';

use Emitters\JsonEmitter\JsonEmitter;
use Emitters\TomlEmitter\TomlEmitter;
use Emitters\YamlEmitter\YamlEmitter;
use JsonParser\Lexer\Lexer;
use Transformer\Evaluator\EvaluationException;
use Transformer\Evaluator\Evaluator;
use Transformer\Evaluator\TransformerContext;
use JsonParser\Parser\Parser;

$output = "";
$inputJson = $_POST['converter-input'] ?? 'null';
$sExpr = $_POST['s-expr-input'] ?? '(. id)';
$outputLang = $_POST['output-language'] ?? 'FormattedJson';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($sExpr === "") {
        $sExpr = '(. id)';
    }

    $ctx = new TransformerContext(__DIR__ . "/../lib/Transformer/std", __DIR__ . "/../plugins");

    $parser = new Parser(new Lexer($inputJson, 4));
    $parserResult = $parser->parse();
    if ($parserResult->isErr()) {
        $output = $parserResult->err()->__toString();
    } else {
        $node = $parserResult->ok();
        try {
            $transformedNode = Evaluator::eval($sExpr, $node, $ctx);
            switch ($outputLang) {
                case "FormattedJson":
                    $output = new JsonEmitter()->emit($transformedNode);
                    break;
                case "YAML":
                    $output = new YamlEmitter()->emit($transformedNode);
                    break;
                case "TOML":
                    $output = new TomlEmitter()->emit($transformedNode);
                    break;
                default:
                    $output = "Unsupported output language";
                    break;
            }
        } catch (EvaluationException $e) {
            $output = $e->getMessage();
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSONConverter</title>
    <link rel="stylesheet" href="styles/styles.css">
    <script type="module" src="javascript/saveModalHandler.js" defer></script>
    <script type="module" src="javascript/dynamicFormAttribute.js" defer></script>
</head>

<body class="flex-body">
    <nav class="sidebar">
        <div class="avatar-placeholder"><img src="img/avatar.png" class="avatar"></div>
        <a href="history.html" class="nav-item">History</a>
        <a href="login.html" class="nav-item">Logout</a>
    </nav>

    <main class="main-content">
        <header class="top-bar">
            <h1>Convert</h1>
        </header>

        <section class="converter">
            <div class="input-area">
                <label for="converter-input">JSON input</label>
                <textarea id="converter-input" name="converter-input" placeholder="Enter your JSON here..."
                    form="convert-form"><?php echo $inputJson; ?></textarea>
                <div class="converter-settings">
                    <form method="post" id="convert-form"><button type="submit" id="convert-button"
                            form="convert-form">Convert</button></form>
                    <div class="output-selector">
                        <label for="output-language">Convert to:</label>
                        <select id="output-language" name="output-language" form="convert-form">
                            <option value="FormattedJson" <?= $outputLang === 'FormattedJson' ? 'selected' : '' ?>>
                                Formatted JSON
                            </option>
                            <option value="YAML" <?= $outputLang === 'YAML' ? 'selected' : '' ?>>
                                YAML
                            </option>
                            <option value="TOML" <?= $outputLang === 'TOML' ? 'selected' : '' ?>>
                                TOML
                            </option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="s-expr-area">
                <label for="s-expr-input">S-Expression</label>
                <textarea id="s-expr-input" name="s-expr-input" placeholder="Enter your S-Expression here..."
                    form="convert-form"><?php echo $sExpr; ?></textarea>
            </div>

            <div class="output-area">
                <label for="converter-output">Output</label>
                <textarea id="converter-output" readonly><?php echo $output; ?></textarea>
                <button id="save-output-button">Save</button>
            </div>
        </section>
    </main>

    <dialog id="save-modal" class="modal">
        <div class="modal-content">
            <button class="modal-close" id="close-save">&times;</button>
            <h2>Save Output</h2>
            <form class="save-form" id="save-form">
                <div class="save-form-container">
                    <label for="save-title">Title</label>
                    <input type="text" id="save-title" placeholder="Title">
                </div>
                <div class="save-form-container">
                    <label for="save-description">Description (optional)</label>
                    <textarea id="save-description"></textarea>
                </div>
                <button type="submit" class="save-button">Save</button>
            </form>
        </div>
    </dialog>

</body>

</html>