<?php
session_start();
require '../../connect.php'; // Adjust the path as necessary

// Get category from GET parameter
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Construct SQL query
$sql = "SELECT package_id, package_title, package_image FROM packages";
if ($category) {
    // Note: Assuming category is a safe value coming from a controlled source
    $sql .= " WHERE category = '$category'";
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages</title>
    <link rel="stylesheet" href="../public/CSS/packages.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&family=Paytone+One&family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    <!--********** Navbar Section *********--->
    <header class="nav-section">
        <div class="nav ">
            <a href="../index.php" class="logo">
                <img src="../Data/logod.png" alt="logo">
                NepTours</a>

            <ul class="navbar">
                <li><a href="../index.php">home</a></li>
                <li><a href="#2">Packages</a></li>
                <li><a href="#3">Services</a></li>
                <li><a href="#">Review</a></li>
                <li><a href="contact.php">contact</a></li>
                <!-- Add "My Bookings" link -->
                <?php if (isset($_SESSION['username'])) { ?>
                    <li><a href="mybookings.php">My Bookings</a></li>
                <?php } ?>
            </ul>

            <ul class="inout">
                <?php if (isset($_SESSION['username'])) { ?>
                    <li style="font-size: 1.6rem; font-weight: 600;color:#fc7c12;"><?php echo $_SESSION['username']; ?></li>
                    <li><a href="../controller/logout.php">Logout</a></li>
                <?php } else { ?>
                    <li><a href="controller/login.php">Login</a></li>
                    <li><a href="controller/register1.php">Signup</a></li>
                <?php } ?>
            </ul>
        </div>
    </header>

    <!-- ***** All Packages Section ***** -->
    <section class="packages">
        <div class="allpack">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $image_path = '../../packagesimage/' . $row["package_image"]; // Construct the image path
                    echo '<a href="package_details.php?package_id=' . $row["package_id"] . '&package_name=' . urlencode($row["package_title"]) . '">';
                    echo '<div class="card">';
                    echo '<div class="card-img">';
                    echo "<img src='{$image_path}' alt='{$row["package_title"]}' style='width:100%;height:100%;'>";
                    echo '</div>';
                    echo '<h1 class="card-title">' . $row["package_title"] . '</h1>';
                    echo '</div>';
                    echo '</a>';
                }
            } else {
                echo "No packages found.";
            }
            $conn->close();
            ?>
        </div>
    </section>

    <script src="../public/main.js"></script>

</body>

</html>
