<?php
session_start();

require '../../connect.php';

// Fetch the user ID based on the logged-in username
$userId = null;
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $userQuery = "SELECT user_id FROM user WHERE username = '$username'";
    $userResult = $conn->query($userQuery);
    if ($userResult->num_rows > 0) {
        $userId = $userResult->fetch_assoc()['user_id'];
    }
}

// Query to get the bookings of the logged-in user
$query = "SELECT b.booking_id, b.package_id, b.num_people, b.package_cost, b.status, p.package_title
          FROM bookings b
          JOIN packages p ON b.package_id = p.package_id
          WHERE b.user_id = '$userId'";

// Execute the query
$result = mysqli_query($conn, $query);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings</title>
    <link rel="stylesheet" href="../public/CSS/mybooking.css">
</head>

<body>
    <!--**********  Navbar Section *********--->
    <header class="nav-section">
        <div class="nav ">
            <a href="../index.php" class="logo">
                <img src="../data/logod.png" alt="logo">
                NepTours
            </a>

            <ul class="navbar">
                <li><a href="../index.php">home</a></li>
                <li><a href="../index.php#2">Packages</a></li>
                <li><a href="../index.php#3">Services</a></li>
                <li><a href="contact.php">contact</a></li>
            </ul>

            <ul class="inout">
                <?php if (isset($_SESSION['username'])) { ?>
                <li style="font-size: 1.6rem; font-weight: 600;color:#fc7c12;">
                    <?php echo $_SESSION['username']; ?>
                </li>
                <li><a href="../controller/logout.php">Logout</a></li>
                <?php } else { ?>
                <li><a href="../controller/login.php">Login</a></li>
                <li><a href="../controller/register1.php">Signup</a></li>
                <?php } ?>
            </ul>
        </div>
    </header>

    <div class="container">
        <h2 class="heading">My Bookings</h2>
        <table>
            <tr>
                <th>Package Title</th>
                <th>Number of People</th>
                <th>Package Cost</th>
                <th>Status</th>
            </tr>
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) { ?>
                <tr>
                    <td><?php echo $row['package_title']; ?></td>
                    <td><?php echo $row['num_people']; ?></td>
                    <td>Rs: <?php echo $row['package_cost']; ?></td>
                    <td style="color: 
                        <?php 
                        if ($row['status'] == 'Complete') {
                            echo 'green';
                        } elseif ($row['status'] == 'Cancelled') {
                            echo 'red';
                        } else {
                            echo 'black';
                        } ?>">
                        <?php echo $row['status']; ?>
                    </td>
                </tr>
            <?php 
                } 
            } else { 
                echo "<tr><td colspan='4'>No bookings found.</td></tr>";
            } 
            ?>
        </table>
    </div>
</body>

</html>

<?php
// Close the database connection
mysqli_close($conn);
?>
