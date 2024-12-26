<!DOCTYPE html>
<html>

<head>
  <title>Booking List</title>
  <link rel="stylesheet" type="text/css" href="booking.css">
  <style>
    .complete {
      color: green;
      font-weight: bold;
    }

    .cancelled {
      color: red;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <header>
    <h1>Tours and Travels</h1>
    <nav>
      <ul>
        <li><a href="admin.php">Dashboard</a></li>
        <li><a href="bookings.php"
            class="<?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>">Bookings</a></li>
        <li><a href="user.php">Users</a></li>
        <li><a href="test.php">Packages</a></li>
        <li><a href="logout.php">Logout</a></li>
      </ul>
    </nav>
  </header>

  <div class="content">
    <h1>Booking List</h1>
    <?php
    // Include the database connection file
    include '../connect.php';
    session_start();

    // Check if user is not logged in, redirect to login.php
    if (!isset($_SESSION['admin_name'])) {
      header("Location: index.php");
      exit(); // Ensure that code execution stops after redirection
    }



    // Check if the form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
      // Get the booking ID and the new status from the form
      $booking_id = $_POST['booking_id'];
      $status = $_POST['status'];

      // Update the booking status in the database
      $query = "UPDATE bookings SET status = '$status' WHERE booking_id = '$booking_id'";
      if (mysqli_query($conn, $query)) {
        // Redirect back to the bookings page with a success message
        header("Location: bookings.php");
        exit();
      } else {
        echo "Error updating status: " . mysqli_error($conn);
      }
    }

    // Get the bookings from the bookings table
    $query = "SELECT b.booking_id, u.username, p.package_title, b.num_people, b.package_cost, b.status 
    FROM bookings b 
    JOIN user u ON b.user_id = u.user_id 
    JOIN packages p ON b.package_id = p.package_id
    ORDER BY FIELD(b.status, 'Pending', 'Complete', 'Cancelled'), b.booking_id DESC";

    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
      // Display the bookings in a table
      echo "<table class='booking-table'>";
      echo "<tr><th>ID</th><th>Username</th><th>Package Title</th><th>Number of People</th><th>Total Cost</th><th>Status</th><th>Action</th></tr>";
      while ($row = mysqli_fetch_array($result)) {
        echo "<tr>";
        echo "<td>" . $row['booking_id'] . "</td>";
        echo "<td>" . $row['username'] . "</td>";
        echo "<td>" . $row['package_title'] . "</td>";
        echo "<td>" . $row['num_people'] . "</td>";
        echo "<td>Rs: " . $row['package_cost'] . "</td>";
        echo "<td>";

        // Display status as text based on the current status
        if ($row['status'] == 'Complete') {
          echo "<span class='complete'>Complete</span>";
        } elseif ($row['status'] == 'Cancelled') {
          echo "<span class='cancelled'>Cancelled</span>";
        } else {
          echo "<span>Pending</span>";
        }
        echo "</td>";
        echo "<td>";

        // Display the Update button only if the status is not Complete or Cancelled
        if ($row['status'] != 'Complete' && $row['status'] != 'Cancelled') {
          echo "<form action='#' method='post'>";
          echo "<input type='hidden' name='booking_id' value='" . $row['booking_id'] . "'>";
          echo "<select name='status'>";
          echo "<option value='Pending' " . ($row['status'] == 'Pending' ? 'elected' : '') . ">Pending</option>";
          echo "<option value='Complete' " . ($row['status'] == 'Complete' ? 'elected' : '') . ">Complete</option>";
          echo "<option value='Cancelled' " . ($row['status'] == 'Cancelled' ? 'elected' : '') . ">Cancelled</option>";
          echo "</select>";
          echo "<input type='submit' value='Update'>";
          echo "</form>";
        }
        echo "</td>";
        echo "</tr>";
      }
      echo "</table>";
    } else {
      echo "No bookings found.";
    }

    // Close the database connection
    mysqli_close($conn);
    ?>
  </div>

  <?php include ('footer.php') ?>
</body>