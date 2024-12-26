<?php
// Start the session 
session_start();

// Include database connection
require '../connect.php'; // Adjust the path to your connect file

$error = "";
$success_message = "";


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $admin_email = $_POST['admin_email'];
    $admin_password = $_POST['admin_password'];

    // Prepare SQL statement
    $stmt = $conn->prepare("SELECT adminID, admin_name, admin_password FROM admin WHERE admin_email = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $admin_email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($admin_password, $row['admin_password'])) { // Corrected variable name
            $_SESSION['adminID'] = $row['adminID'];
            $_SESSION['admin_name'] = $row['admin_name'];
            header("Location: admin.php");

            exit();
        } else {
            $error = "Invalid email or password.";
        }
    } else {
        $error = "Invalid email or password.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url(../user/data/bg_login.png);
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
            margin: 19rem 20rem;
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

        .success-message {
            padding: 10px;
            background-color: #04AA6D;
            color: white;
            border-radius: 4px;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <h2>Login</h2>
        <!-- Display error message if present -->
        <?php if (!empty($error)) { ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php } ?>

        <!-- Login form -->
        <form method="post" action="" id="login-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="admin_email" required><br>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="admin_password" required><br>
                <span class="toggle-password" onclick="togglePasswordVisibility()">
                    <i class="fas fa-eye"></i>
                </span>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
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
    </script>
</body>

</html>