<!DOCTYPE html>
<html>

<head>
  <title>user</title>
  <link rel="stylesheet" href="user.css">
</head>

<body>
  <header>
    <h1>Tours and Travels</h1>
    <nav>
      <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="bookings.php">Bookings</a></li>
        <li><a href="user.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'user.php' ? 'active' : ''; ?>">Users</a></li>
        <li><a href="test.php">Packages</a></li>
        <li><a href="Logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <?php

  require '../connect.php';

  // Handle delete action if requested
  if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

     
    // Delete bookings associated with the user first
    $delete_bookings_query = "DELETE FROM bookings WHERE user_id=$id";
    mysqli_query($conn, $delete_bookings_query);

    $query = "DELETE FROM user WHERE user_id=$id";
    mysqli_query($conn, $query);
  }

  // Query the database for user data
  $query = 'SELECT * FROM user';
  $result = mysqli_query($conn, $query);

  // Generate the HTML for the table
  $html = '<div class="user-list">
<h2>User List</h2>
<table>
  <thead>
    <tr>
      <th>Id</th>
      <th>Username</th>
      <th>E-mail</th>
      <th>Password</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>';

  // Loop through query result and output user data in table rows
  while ($row = mysqli_fetch_assoc($result)) {
    $id = $row['user_id'];
    if ($row['username'] == 'admin') {
      continue; // skip this row
    }
    $html .= "<tr>";
    $html .= "<td>" . $id . "</td>";
    $html .= "<td>" . $row['username'] . "</td>";
    $html .= "<td>" . $row['user_email'] . "</td>";
    $html .= "<td>" . $row['user_password'] . "</td>";
    $html .= "<td>";
    $html .= "<a href='?delete=" . $id . "' class='button delete'>Delete</a>";
    $html .= "</td>";
    $html .= "</tr>";
  }

  $html .= '</tbody>
</table>
</div>';

  // Display the HTML
  echo $html;

  // Close the database connection
  mysqli_close($conn);
  ?>


  </table>
  </div>';




</body>

</html>
<?php include ('footer.php'); ?>