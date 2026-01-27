<?php
require_once __DIR__ . '/../autoload.php';
use db\repository\SessionRepository;
use db\repository\UserRepository;
use db\DBClient;
use services\AuthService;
use utility\FormErrors;

session_start();

$loginUsername = '';
$loginPassword = '';
$loginErrors = new FormErrors();

$registerUsername = '';
$registerPassword = '';
$registerConfirmPassword = '';
$registerErrors = new FormErrors();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = new DBClient(DB_CLIENT_HOST, DB_CLIENT_PORT, DB_CLIENT_DBNAME, DB_CLIENT_USER, DB_CLIENT_PASS);
    $sessions = new SessionRepository($db);
    $users = new UserRepository($db);
    $auth = new AuthService($sessions, $users);


    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password-confirmation'] ?? '';

    // Login logic
    if (isset($_POST['action']) && $_POST['action'] === 'login') {
        $loginErrors = $auth->validateLoginInfo($username, $password);
        if (!$loginErrors->hasErrors) {
            $auth->loginUser();
            header('Location: index.php');
            exit;
        }
        $loginUsername = $username;
        $loginPassword = $password;
    }

    // Register logic
    if (isset($_POST['action']) && $_POST['action'] === 'register') {
        $registerErrors = $auth->validateRegisterInfo($username, $password, $passwordConfirmation);
        if (!$registerErrors->hasErrors) {
            $auth->registerUser($username, $password);
            header('Location: index.php');
            exit;
        }
        $registerUsername = $username;
        $registerPassword = $password;
        $registerConfirmPassword = $passwordConfirmation;
    }
}
?>

<?php if ($registerErrors->hasErrors): ?>
    <script>
        // We want to keep the register modal open if we have errors
        // But PHP reloads the whole page so we need to automatically open the modal
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('register-modal');
            if (modal) {
                modal.showModal();
            }
        });
    </script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JSONConverter</title>
    <link rel="stylesheet" href="styles/styles.css">
</head>

<body>
    <main class="login-container">
        <section class="login-card">
            <h1>JSON Converter</h1>
            <form method="post" class="login-form" id="login-form">
                <div class="login-form-container">
                    <label for="username">Username</label>
                    <input type="text" id="login-username" placeholder="Enter Username" name="username"
                        value="<?php echo htmlspecialchars($loginUsername) ?>">
                    <p class="error" id="login-username-error"><?php echo $loginErrors->username ?></p>
                </div>
                <div class="login-form-container">
                    <label for="password">Password</label>
                    <input type="password" id="login-password" placeholder="Enter Password" name="password"
                        value="<?php echo htmlspecialchars($loginPassword) ?>">
                    <p class="error" id="login-password-error"><?php echo $loginErrors->password ?></p>
                </div>
                <button type="submit" class="login-button" value="login" name="action">Login</button>
            </form>
            <p class="register-link">Don't have an account? <a href="#" id="register-link">Register</a></p>
        </section>
    </main>

    <dialog id="register-modal" class="modal">
        <div class="modal-content">
            <button class="modal-close" id="close-register">&times;</button>
            <h2>Register</h2>
            <form method="post" class="register-form" id="register-form">
                <div class="register-form-container">
                    <label for="register-username">Username</label>
                    <input type="text" id="register-username" name="username" placeholder="Enter Username"
                        value="<?php echo htmlspecialchars($registerUsername) ?>">
                    <p class="error" id="register-username-error"><?php echo $registerErrors->username ?></p>
                </div>
                <div class="register-form-container">
                    <label for="register-password">Password</label>
                    <input type="password" id="register-password" name="password" placeholder="Enter Password"
                        value="<?php echo htmlspecialchars($registerPassword) ?>">
                    <p class="error" id="register-password-error"><?php echo $registerErrors->password ?></p>
                </div>
                <div class="register-form-container">
                    <label for="register-password-confirmation">Confirm password</label>
                    <input type="password" id="register-password-confirmation" name="password-confirmation"
                        placeholder="Enter Password" value="<?php echo htmlspecialchars($registerConfirmPassword) ?>">
                    <p class="error" id="register-password-confirmation-error">
                        <?php echo $registerErrors->passwordConfirmation ?></p>
                </div>
                <button type="submit" class="register-button" value="register" name="action">Register</button>
            </form>
        </div>
    </dialog>

    <script src="javascript/registerModalHandler.js"></script>
    <script src="javascript/loginHandler.js"></script>

</body>

</html>