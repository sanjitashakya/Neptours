<?php
session_start();

include ' ../../../../connect.php';

$error = '';
if (isset($_POST['submit'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $user_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $user_email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_password = mysqli_real_escape_string($conn, $_POST['password']);
    $cpass = mysqli_real_escape_string($conn, $_POST['cpass']);

    if ($user_password !== $cpass) {
        $error = 'Password and confirm password do not match';
    } else {
        $emailquery = "SELECT * FROM user WHERE user_email = '$user_email' ";
        $query = mysqli_query($conn, $emailquery);

        if (mysqli_num_rows($query) > 0) {
            $error = 'Email already exists';
        } else {
            $user_password = password_hash($user_password, PASSWORD_BCRYPT);
            $insertquery = "INSERT INTO user (username, user_phone, user_email, user_password) VALUES ('$username', '$user_phone', '$user_email', '$user_password')";
            $iquery = mysqli_query($conn, $insertquery);
            if ($iquery) {
                 // Store user information in session
                 $_SESSION['username'] = $username;

                echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Registration successful!",
                            showConfirmButton: true,
                        }).then(function() {
                            window.location.href = "../index.php";
                        });
                    });
                </script>';
            } else {
                $error = 'Failed to register user';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(../data/bg_login.png);
            background-position: center;
            background-size: cover;
            justify-content: center;
            align-items: center;
            height: 100%;
            margin: 0;
        }

        .form-container {
            background: transparent;
            border-radius: 12px;
            box-shadow: 0px 4px 20px rgba(0, 0, 0, 0.4);
            padding: 20px;
            width: 300px;
            margin: 10rem 20rem; 
        }

        .form-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .form-group input:focus {
            outline: none;
            border-color: #2a2185;
        }

        .error-message {
            padding: 20px;
            background-color: #f44336;
            color: white;
            margin-bottom: 20px;
            border-radius: 4px;
        }

        .closebtn {
            margin-left: 15px;
            color: white;
            font-weight: bold;
            float: right;
            font-size: 22px;
            line-height: 20px;
            cursor: pointer;
            transition: 0.3s;
        }

        .closebtn:hover {
            color: black;
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

        .switch-form {
            margin-top: 10px;
            text-align: center;
        }

        .switch-form a {
            color: #2a2185;
            text-decoration: none;
        }

        .error-input {
            border-color: red !important;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Register</h2>
        <?php if (!empty($error)) { ?>
            <div class="error-message">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <form id="register-form" action="#" method="post">
            <div class="form-group">
                <label for="register-username">Username</label>
                <input type="text" id="register-username" name="username" required>
            </div>
            <div class="form-group">
                <label for="register-phone">Phone number</label>
                <input type="text" id="register-phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="register-email">Email</label>
                <input type="email" id="register-email" name="email" required>
            </div>
            <div class="form-group">
                <label for="register-password">Password</label>
                <input type="password" id="register-password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="cpass" required>
            </div>
            <button type="submit" name="submit" class="btn">Register</button>
        </form>
        <div class="switch-form">
            <p>Already have an account? <a href="login.php" id="switch-to-login">Login</a></p>
        </div>
    </div>
</body>
</html>
