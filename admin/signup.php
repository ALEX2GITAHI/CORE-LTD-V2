<?php

session_start();

require_once __DIR__ . '/../includes/db.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if ($name === '' || $email === '' || $phone === '' || $password === '') {

        $message = 'Please fill in all required fields.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } elseif (strlen($password) < 8) {

        $message = 'Password must be at least 8 characters.';
        $messageType = 'error';
    } elseif ($password !== $confirmPassword) {

        $message = 'Passwords do not match.';
        $messageType = 'error';
    } else {

        /*
        |--------------------------------------------------------------------------
        | CHECK IF EMAIL ALREADY EXISTS
        |--------------------------------------------------------------------------
        */

        $check = $conn->prepare("
            SELECT id
            FROM users
            WHERE email = ?
            LIMIT 1
        ");

        $check->bind_param("s", $email);

        $check->execute();

        $result = $check->get_result();

        if ($result->num_rows > 0) {

            $message = 'An account with this email already exists.';
            $messageType = 'error';
        } else {

            /*
            |--------------------------------------------------------------------------
            | HASH PASSWORD
            |--------------------------------------------------------------------------
            */

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            /*
            |--------------------------------------------------------------------------
            | CREATE ADMIN USER
            |--------------------------------------------------------------------------
            */

            $role = 'admin';
            $status = 'active';

            $stmt = $conn->prepare("
                INSERT INTO users
                (
                    name,
                    email,
                    phone,
                    password,
                    role,
                    status
                )
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ssssss",
                $name,
                $email,
                $phone,
                $hashedPassword,
                $role,
                $status
            );

            if ($stmt->execute()) {

                $message =
                    'Admin account created successfully. You can now log in.';

                $messageType = 'success';

                /*
                |--------------------------------------------------------------------------
                | CLEAR FORM VALUES
                |--------------------------------------------------------------------------
                */

                $name = '';
                $email = '';
                $phone = '';
            } else {

                $message =
                    'Unable to create the account. Please try again.';

                $messageType = 'error';
            }

            $stmt->close();
        }

        $check->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CORE LTD - Admin Sign Up</title>

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

    .signup-container {
        width: 100%;
        max-width: 480px;

        padding: 20px;
    }

    .signup-box {
        background: #ffffff;

        padding: 40px;

        border-radius: 12px;

        box-shadow:
            0 10px 35px rgba(0, 0, 0, 0.08);
    }

    .logo {
        text-align: center;

        margin-bottom: 25px;
    }

    .logo h1 {
        margin: 0;

        font-size: 30px;

        color: #222;
    }

    .logo p {
        margin-top: 7px;

        color: #777;

        font-size: 14px;
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;

        margin-bottom: 7px;

        font-size: 14px;

        font-weight: 600;

        color: #333;
    }

    .form-group input {
        width: 100%;

        padding: 13px 14px;

        border: 1px solid #d7d7d7;

        border-radius: 6px;

        font-size: 15px;

        outline: none;
    }

    .form-group input:focus {
        border-color: #222;
    }

    .btn-signup {
        width: 100%;

        padding: 14px;

        border: none;

        border-radius: 6px;

        background: #222;

        color: #ffffff;

        font-size: 16px;

        font-weight: 700;

        cursor: pointer;
    }

    .btn-signup:hover {
        background: #000;
    }

    .message {
        padding: 12px 14px;

        margin-bottom: 20px;

        border-radius: 6px;

        font-size: 14px;
    }

    .message.error {
        background: #fdecec;

        color: #b42318;
    }

    .message.success {
        background: #eaf7ee;

        color: #18743a;
    }

    .login-link {
        margin-top: 22px;

        text-align: center;

        font-size: 14px;

        color: #666;
    }

    .login-link a {
        color: #222;

        font-weight: 700;

        text-decoration: none;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .required {
        color: #d00;
    }
    </style>

</head>

<body>

    <div class="signup-container">

        <div class="signup-box">

            <div class="logo">

                <h1>CORE LTD</h1>

                <p>Administrator Sign Up</p>

            </div>


            <?php if ($message !== ''): ?>

            <div class="message <?= htmlspecialchars($messageType) ?>">

                <?= htmlspecialchars($message) ?>

            </div>

            <?php endif; ?>


            <form method="POST" action="">

                <div class="form-group">

                    <label>
                        Full Name
                        <span class="required">*</span>
                    </label>

                    <input type="text" name="name" value="<?= htmlspecialchars($name ?? '') ?>" required>

                </div>


                <div class="form-group">

                    <label>
                        Email Address
                        <span class="required">*</span>
                    </label>

                    <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required>

                </div>


                <div class="form-group">

                    <label>
                        Phone Number
                        <span class="required">*</span>
                    </label>

                    <input type="text" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>" required>

                </div>


                <div class="form-group">

                    <label>
                        Password
                        <span class="required">*</span>
                    </label>

                    <input type="password" name="password" minlength="8" required>

                </div>


                <div class="form-group">

                    <label>
                        Confirm Password
                        <span class="required">*</span>
                    </label>

                    <input type="password" name="confirm_password" minlength="8" required>

                </div>


                <button type="submit" class="btn-signup">
                    Create Admin Account
                </button>

            </form>


            <div class="login-link">

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </div>

        </div>

    </div>

</body>

</html>