<?php
// Include the database connection file
include '../connect.php';

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
        // Redirect back to the bookings page with an error message
        header("Location: bookings.php");
        exit();
    }
}

// Close the database connection
mysqli_close($conn);
