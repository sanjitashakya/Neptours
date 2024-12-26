<?php
$error = ""; // Initialize the error variable

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require ' ../../../../connect.php';
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $phone = $_POST['phone'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['pass'];
    $cpass = $_POST['cpass'];
    // Check if password and confirm password match
    if ($pass !== $cpass) {
        $error = 'Password and Confirm Password do not match.';
    } else {
        // Hash the password
        $hashed_pass = password_hash($pass, PASSWORD_DEFAULT);

        // Check if user already exists
        $check_query = "SELECT * FROM user WHERE username = '$username'";
        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {
            $error = 'Username already taken';
        } else {
            // Insert the user info into the database with hashed password
            $sql = "INSERT INTO user (username,phone,email, password) VALUES ('$username', '$phone','$email', '$hashed_pass')";
            $result = mysqli_query($conn, $sql);

            if ($result) {
                header("location: ../index.php");
                exit; // Add an exit statement after redirection
            } else {
                $error = 'Registration failed. Please try again.';
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
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 300px;
            margin: 10rem auto; /* Center the form horizontally */
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

        /* Add red border to confirm password input */
        .error-input {
            border-color: red !important;
        }
    </style>
</head>

<body>

    <div class="form-container">
        <h2>Register</h2>
        <!-- Display error message if present -->
        <?php if (!empty($error)) { ?>
            <div class="error-message">
                <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                <?php echo $error; ?>
            </div>
        <?php } ?>
        <!-- Registration form -->
        <form id="register-form" action="#" method="post">
            <div class="form-group">
                <label for="register-username">Username</label>
                <input type="text" id="register-username" name="username" required>
            </div>
            <div class="form-group">
                <label for="register-phone">Phone number</label>
                <input type="int" id="register-phone" name="phone" required>
            </div>
            <div class="form-group">
                <label for="register-email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="register-password">Password</label>
                <input type="password" id="register-password" name="pass" required>
            </div>
            <div class="form-group">
                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="cpass" required onkeyup="checkPasswordMatch()">
            </div>
            <button type="submit" class="btn">Register</button>
        </form>
        <div class="switch-form">
            <p>Already have an account? <a href="login.php" id="switch-to-login">Login</a></p>
        </div>
    </div>

    
</body>

</html>
