<?php
include '../connect.php';
// Check if form is submitted
if (isset($_POST["submit"])) {
    // Retrieve data from form
    $username = $_POST["username"];
    $package_title = $_POST["package_title"];
    $status = $_POST["status"];

    // Update booking details in database
    $sql = "UPDATE bookings SET username='$username', package_title='$package_title', status='$status' WHERE booking_id=" . $_GET["edit_id"];
    $result = $conn->query($sql);

    if ($result === true) {
        echo "<script>alert('Edit successfully!')</script>";
        header("refresh:0;url=bookings.php");
} else {
    echo "Error updating booking: " . $conn->error;
}

}

// Query to retrieve booking details
$sql = "SELECT booking_id, username, package_title, status FROM bookings WHERE booking_id=" . $_GET["edit_id"];
$result = $conn->query($sql);

if ($result !== false && $result->num_rows == 1) {
    // Output edit form
    $row = $result->fetch_assoc();
    ?>
    <h1>Edit Booking</h1>
    <form method="POST">
        <label for="username">User ID:</label>
        <input type="text" name="username" value="<?php echo $row["username"]; ?>"><br>

        <label for="package_id">Package ID:</label>
        <input type="text" name="package_id" value="<?php echo $row["package_id"]; ?>"><br>

        <label for="status">Status:</label>
        <select name="status">
            <option value="pending" <?php if ($row["status"] == "pending") echo "selected"; ?>>Pending</option>
            <option value="confirmed" <?php if ($row["status"] == "confirmed") echo "selected"; ?>>Confirmed</option>
            <option value="cancelled" <?php if ($row["status"] == "cancelled") echo "selected"; ?>>Cancelled</option>
        </select><br>

        <label for="schedule">Schedule:</label>
        <input type="text" name="schedule" value="<?php echo $row["schedule"]; ?>"><br>

        <input type="submit" name="submit" value="Update">
        <a href="bookings.php"><button type="button">Back</button></a>
    </form>
    <?php
} else {
    echo "Booking not found!";
}

// Close database connection
$conn->close();
?>
<style>

    h1 {
        text-align: center;
    }

    form {
  width: 50%;
  margin: 0 auto;
  font-family: Arial, sans-serif;
  font-size: 16px;
  line-height: 1.5;
  color: #333;
  padding: 20px;
  border: 1px solid #ddd;
  border-radius: 5px;
  background-image: url('your-background-image-url.jpg');
  background-size: cover;
}

    label {
        display: block;
        margin-bottom: 10px;
    }

    input[type="text"], select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        margin-bottom: 20px;
    }

    input[type="submit"] {
        background-color: #005eff;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 5px;
        font-weight: bold;
    }
    button{
        background-color: gray;
        border: none;
        color: white;
        padding: 10px 20px;
        text-align: center;
        text-decoration: none;
        display: inline-block;
        font-size: 16px;
        margin: 4px 2px;
        cursor: pointer;
        border-radius: 5px;
        font-weight: bold;
    }
    
</style>

