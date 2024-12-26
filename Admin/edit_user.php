<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit User</title>
  <style>
    /* Main styles for the edit form */
    body {
      background-color: #f5f5f5;
      font-family: sans-serif;
    }

    .edit-user {
      background-color: #fff;
      border-radius: 10px;
      box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      margin: 50px auto;
      max-width: 500px;
      padding: 20px;
    }

    .edit-user h2 {
      color: #444;
      font-size: 24px;
      margin-bottom: 20px;
    }

    .edit-user label {
      color: #777;
      display: block;
      font-size: 14px;
      margin-bottom: 5px;
    }

    .edit-user input[type="text"],
    .edit-user input[type="email"],
    .edit-user input[type="password"],
    .edit-user textarea {
      border: 1px solid #ddd;
      border-radius: 5px;
      box-sizing: border-box;
      display: block;
      font-size: 16px;
      margin-bottom: 10px;
      padding: 10px;
      width: 100%;
    }

    .edit-user textarea {
      height: 100px;
    }

    .edit-user input[type="submit"] {
      background-color: #005eff;
      border: none;
      border-radius: 5px;
      color: #fff;
      cursor: pointer;
      font-size: 16px;
      padding: 10px;
      transition: background-color 0.3s;
    }

    .edit-user input[type="submit"]:hover {
      background-color: blue;
    }

    .edit-user button {
      background-color: #777;
      border: none;
      border-radius: 5px;
      color: #fff;
      cursor: pointer;
      font-size: 16px;
      padding: 10px;
      margin-left: 10px;
      transition: background-color 0.3s;
    }

    .edit-user button:hover {
      background-color: #555;
    }
  </style>
</head>

<body>

  <?php
  require '../connect.php';

// Handle form submission if requested
if (isset($_POST['submit'])) {
  $id = intval($_POST['user_id']);
  $username = mysqli_real_escape_string($conn, $_POST['username']);
  $user_email = mysqli_real_escape_string($conn, $_POST['user_email']);
  $user_password = mysqli_real_escape_string($conn, $_POST['user_password']);
  
  // Hash the password before saving
  $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
  
  $query = "UPDATE user SET username='$username', user_email='$user_email', user_password='$hashed_password' WHERE user_id=$id";
  $result = mysqli_query($conn, $query);

  // Check if the update was successful
  if ($result && mysqli_affected_rows($conn) > 0) {
    echo "<div class='message'>Edited successfully</div>";
  } else {
    echo "<div class='message'>Error updating record: " . mysqli_error($conn) . "</div>";
  }

  header('Location: user.php');
  exit();
}

  // Query the database for user data
  $user_id = intval($_GET['id']);
  $query = "SELECT * FROM user WHERE user_id = $user_id";
  $result = mysqli_query($conn, $query);
  $row = mysqli_fetch_assoc($result);

  // Display the HTML for the form
  if ($row) {
    echo '
    <div class="edit-user">
      <h2>Edit User</h2>
      <form method="post">
        <input type="hidden" name="user_id" value="' . $row['user_id'] . '">

        <label for="username">Username:</label>
        <input type="text" name="username" value="' . htmlspecialchars($row['username']) . '"><br>

        <label for="user_email">E-mail:</label>
        <input type="email" name="user_email" value="' . htmlspecialchars($row['user_email']) . '"><br>

        <label for="user_password">Password:</label>
        <input type="password" name="user_password" value="' . htmlspecialchars($row['user_password']) . '"><br>

        <input type="submit" name="submit" value="Update">
        <a href="user.php"><button type="button">Back</button></a>
      </form>
    </div>';
  } else {
    echo '<div class="message">User not found.</div>';
  }

  // Close the database connection
  mysqli_close($conn);
  ?>

</body>

</html>
