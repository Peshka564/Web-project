<?php

require_once __DIR__ . '/../autoload.php';
require_once __DIR__ . '/../config.php';

use db\repository\HistoryRepository;
use PageModels\ConverterPageModel;
use db\repository\SessionRepository;
use db\repository\UserRepository;
use db\DBClient;
use services\AuthService;

session_start();

$db = new DBClient();
$history = new HistoryRepository($db);
$sessions = new SessionRepository($db);
$users = new UserRepository($db);
$auth = new AuthService($sessions, $users);

$auth->guard();

$model = new ConverterPageModel($history, $sessions);

$outputLang = $model->getOutputLanguage();
$output = $model->convert();

if ($model->isSaveAction()) {
    $model->saveHistory();
    $id = $model->getId();
    header("Location: converter.php?id=" . $id);
    exit;
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
        <a href="history.php" class="nav-item">History</a>
        <a href="login.php" class="nav-item">Logout</a>
        <?php if ($model->hasId()): ?>
            <a href="converter.php" class="nav-item">+</a>
        <?php endif; ?>
    </nav>

    <main class="main-content">
        <header class="top-bar">
            <h1>Convert</h1>
        </header>

        <section class="converter">
            <div class="input-area">
                <label for="converter-input">JSON input</label>
                <textarea id="converter-input" name="converter-input" placeholder="Enter your JSON here..."
                    form="convert-form"><?php echo htmlspecialchars($model->getJsonInput()); ?></textarea>
                <div class="converter-settings">
                    <form method="post" id="convert-form"><button type="submit" id="convert-button" form="convert-form"
                            name="action" value="convert">Convert</button></form>
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
                    form="convert-form"><?php echo htmlspecialchars($model->getSExpr()); ?></textarea>
            </div>

            <div class="output-area">
                <label for="converter-output">Output</label>
                <textarea id="converter-output" readonly><?php echo htmlspecialchars($output); ?></textarea>
                <button id="save-output-button">Save</button>
            </div>
        </section>
    </main>

    <dialog id="save-modal" class="modal">
        <div class="modal-content">
            <button class="modal-close" id="close-save">&times;</button>
            <h2>Save Output</h2>
            <form method="post" class="save-form" id="save-form">
                <input type="hidden" name="action" value="save">
                <div class="save-form-container">
                    <label for="save-title">Title</label>
                    <input type="text" name="save-title" id="save-title"
                        value="<?php echo htmlspecialchars($model->getTitle()) ?>" placeholder="Title">
                    <p class="error" id="save-title-error"></p>
                </div>
                <div class="save-form-container">
                    <label for="save-description">Description (optional)</label>
                    <textarea id="save-description"
                        name="save-description"><?php echo htmlspecialchars($model->getDescription()) ?></textarea>
                </div>
                <button type="submit" class="save-button">Save</button>
            </form>
        </div>
    </dialog>

</body>

</html>