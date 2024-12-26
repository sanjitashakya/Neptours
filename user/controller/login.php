<?php
session_start();

if (isset($_SESSION["email"])) {
    header("Location: ../index.php");
    exit;
}

require ' ../../../../connect.php';

$error = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_password = $_POST['password'];

    $user_sql = "SELECT * FROM user WHERE user_email='$user_email'";
    $user_result = mysqli_query($conn, $user_sql);

    if ($user_result && mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        if (password_verify($user_password , $user_data['user_password'])) {
            $_SESSION['username'] = $user_data['username'];
            $_SESSION['email'] = $user_data['user_email'];
            $success_message = 'Login successful!';
        } else {
            $error = 'Invalid Username or Password';
        }
    } else {
        $error = 'Invalid Username or Password';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(../data/bg_login.png);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: center;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
        }

        .login-container {
            background: transparent;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.4);
            padding: 20px;
            width: 300px;
            margin: 16rem 20rem;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: calc(100% - 30px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2a2185;
        }

        .toggle-password {
            position: absolute;
            top: 68%;
            right: 2.3rem;
            transform: translateY(-50%);
            padding: 5px;
            cursor: pointer;
            font-size: 1.2rem;
        }

        .btn {
            background-color: #2a2185;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 20px;
            cursor: pointer;
            width: 100%;
            display: block;
            text-align: center;
            font-size: 16px;
        }

        .btn:hover {
            background-color: #1c166d;
        }

        .error-message {
            padding: 10px;
            background-color: #f44336;
            color: white;
            border-radius: 4px;
            margin-bottom: 10px;
            text-align: center;
        }

        .switch-form {
            margin-top: 10px;
            text-align: center;
        }

        .switch-form a {
            color: #2a2185;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error)) { ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php } ?>
        <form id="login-form" action="" method="post">
            <div class="form-group">
                <label for="login-email">Email:</label>
                <input type="email" id="login-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="login-password">Password:</label>
                <input type="password" id="login-password" name="password" required>
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <div class="switch-form">
            <p>Don't have an account? <a href="register1.php" id="switch-to-register">Register</a></p>
        </div>
    </div>
    <script>
        function togglePasswordVisibility() {
            var passwordField = document.getElementById("login-password");
            if (passwordField.type === "password") {
                passwordField.type = "text";
            } else {
                passwordField.type = "password";
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            <?php if (!empty($success_message)) { ?>
                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "<?php echo $success_message; ?>",
                    showConfirmButton: true,
                }).then(function() {
                    window.location.href = "../index.php";
                });
            <?php } ?>
        });
    </script>
</body>
</html>
