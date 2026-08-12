<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

$message = '';
$messageType = '';

/*
|--------------------------------------------------------------------------
| ALREADY LOGGED IN
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['admin_id'])) {
    header("Location: index.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($email === '' || $password === '') {

        $message = 'Please enter your email and password.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {

        /*
        |--------------------------------------------------------------------------
        | FIND ADMIN USER
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare("
            SELECT
                id,
                name,
                email,
                phone,
                password,
                role,
                status
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        /*
        |--------------------------------------------------------------------------
        | CHECK ACCOUNT
        |--------------------------------------------------------------------------
        */

        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();

            /*
            |--------------------------------------------------------------------------
            | CHECK STATUS
            |--------------------------------------------------------------------------
            */

            if ((int)$user['status'] !== 1) {

                $message =
                    'Your account is inactive. Please contact the administrator.';

                $messageType = 'error';
            }

            /*
            |--------------------------------------------------------------------------
            | CHECK ROLE
            |--------------------------------------------------------------------------
            */ elseif (strtolower($user['role']) !== 'admin') {

                $message =
                    'You do not have permission to access the admin panel.';

                $messageType = 'error';
            }

            /*
            |--------------------------------------------------------------------------
            | VERIFY PASSWORD
            |--------------------------------------------------------------------------
            */ elseif (!password_verify($password, $user['password'])) {

                $message =
                    'Incorrect email or password.';

                $messageType = 'error';
            }

            /*
            |--------------------------------------------------------------------------
            | LOGIN SUCCESSFUL
            |--------------------------------------------------------------------------
            */ else {

                /*
                |--------------------------------------------------------------------------
                | REGENERATE SESSION ID
                |--------------------------------------------------------------------------
                */

                session_regenerate_id(true);


                /*
                |--------------------------------------------------------------------------
                | STORE ADMIN SESSION
                |--------------------------------------------------------------------------
                */

                $_SESSION['admin_id'] = $user['id'];

                $_SESSION['admin_name'] = $user['name'];

                $_SESSION['admin_email'] = $user['email'];

                $_SESSION['admin_phone'] = $user['phone'];

                $_SESSION['admin_role'] = $user['role'];


                /*
                |--------------------------------------------------------------------------
                | UPDATE LAST LOGIN
                |--------------------------------------------------------------------------
                */

                $update = $conn->prepare("
                    UPDATE users
                    SET last_login = NOW()
                    WHERE id = ?
                ");

                $update->bind_param(
                    "i",
                    $user['id']
                );

                $update->execute();

                $update->close();


                /*
                |--------------------------------------------------------------------------
                | GO TO DASHBOARD
                |--------------------------------------------------------------------------
                */

                header("Location: index.php");

                exit;
            }
        } else {

            $message =
                'Incorrect email or password.';

            $messageType = 'error';
        }

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CORE LTD - Admin Login</title>


    <style>
    * {
        box-sizing: border-box;
    }


    body {

        margin: 0;

        min-height: 100vh;

        display: flex;

        align-items: center;

        justify-content: center;

        background: #f4f6f8;

        font-family:
            Arial,
            Helvetica,
            sans-serif;
    }


    .login-container {

        width: 100%;

        max-width: 450px;

        padding: 20px;
    }


    .login-box {

        background: #ffffff;

        padding: 40px;

        border-radius: 12px;

        box-shadow:
            0 10px 35px rgba(0, 0, 0, 0.08);
    }


    .logo {

        text-align: center;

        margin-bottom: 30px;
    }


    .logo h1 {

        margin: 0;

        font-size: 32px;

        color: #222;
    }


    .logo p {

        margin-top: 8px;

        color: #777;

        font-size: 14px;
    }


    .form-group {

        margin-bottom: 20px;
    }


    .form-group label {

        display: block;

        margin-bottom: 8px;

        font-size: 14px;

        font-weight: 600;

        color: #333;
    }


    .form-group input {

        width: 100%;

        padding: 14px;

        border: 1px solid #d7d7d7;

        border-radius: 6px;

        font-size: 15px;

        outline: none;
    }


    .form-group input:focus {

        border-color: #222;
    }


    .password-wrapper {

        position: relative;
    }


    .password-wrapper input {

        padding-right: 55px;
    }


    .toggle-password {

        position: absolute;

        right: 12px;

        top: 50%;

        transform: translateY(-50%);

        border: none;

        background: transparent;

        cursor: pointer;

        color: #666;

        font-size: 14px;
    }


    .btn-login {

        width: 100%;

        padding: 14px;

        border: none;

        border-radius: 6px;

        background: #222;

        color: #fff;

        font-size: 16px;

        font-weight: 700;

        cursor: pointer;

        transition: 0.2s ease;
    }


    .btn-login:hover {

        background: #000;

        transform: translateY(-1px);
    }


    .message {

        padding: 13px 15px;

        margin-bottom: 22px;

        border-radius: 6px;

        font-size: 14px;
    }


    .message.error {

        background: #fdecec;

        color: #b42318;
    }


    .signup-link {

        text-align: center;

        margin-top: 25px;

        font-size: 14px;

        color: #666;
    }


    .signup-link a {

        color: #222;

        font-weight: 700;

        text-decoration: none;
    }


    .signup-link a:hover {

        text-decoration: underline;
    }


    .back-home {

        text-align: center;

        margin-top: 15px;

        font-size: 13px;
    }


    .back-home a {

        color: #777;

        text-decoration: none;
    }


    .back-home a:hover {

        text-decoration: underline;
    }
    </style>

</head>


<body>


    <div class="login-container">

        <div class="login-box">


            <div class="logo">

                <h1>CORE LTD</h1>

                <p>Administrator Login</p>

            </div>


            <?php if ($message !== ''): ?>

            <div class="message <?= htmlspecialchars($messageType) ?>">

                <?= htmlspecialchars($message) ?>

            </div>

            <?php endif; ?>


            <form method="POST" action="">


                <div class="form-group">

                    <label for="email">
                        Email Address
                    </label>

                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                        autocomplete="username" required>

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>


                    <div class="password-wrapper">

                        <input type="password" id="password" name="password" autocomplete="current-password" required>


                        <button type="button" class="toggle-password" onclick="togglePassword()">
                            Show
                        </button>

                    </div>

                </div>


                <button type="submit" class="btn-login">
                    Login
                </button>


            </form>


            <div class="signup-link">

                Don't have an admin account?

                <a href="signup.php">
                    Sign Up
                </a>

            </div>


            <div class="back-home">

                <a href="../index.php">
                    ← Back to Website
                </a>

            </div>


        </div>

    </div>


    <script>
    function togglePassword() {

        const password =
            document.getElementById('password');

        const button =
            document.querySelector('.toggle-password');


        if (password.type === 'password') {

            password.type = 'text';

            button.textContent = 'Hide';

        } else {

            password.type = 'password';

            button.textContent = 'Show';

        }

    }
    </script>


</body>

</html>