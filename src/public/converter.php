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
                    <textarea id="converter-input" placeholder="Enter your JSON here..."></textarea>
                    <div class="converter-settings">
                        <button id="convert-button">Convert</button>
                        <div class="output-selector">
                            <label for="output-language">Convert to:</label>
                            <select id="output-language">
                                <option value="FormattedJson">
                                    Formatted JSON
                                </option>
                                <option value="YAML">
                                    YAML
                                </option>
                                <option value="TOML">
                                    TOML
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="s-expr-area">
                    <label for="s-expr-input">S-Expression</label>
                    <textarea id="s-expr-input" placeholder="Enter your S-Expression here..." ></textarea>
                </div>

                <div class="output-area">
                    <label for="converter-output">Output</label>
                    <textarea id="converter-output" readonly></textarea>
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
                    <p class="error" id="save-title-error"></p>
                </div>
                <div class="save-form-container">
                    <label for="save-description">Description (optional)</label>
                    <textarea id="save-description"></textarea>
                </div>
                <button type="submit" class="save-button">Save</button>
            </form>
        </div>
    </dialog>

    <script src="javascript/saveModalHandler.js"></script>

    </body>
</html>