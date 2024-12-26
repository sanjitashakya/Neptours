<?php
session_start();

require '../../connect.php';

// Check if user is not logged in, redirect to login.php
if (!isset($_SESSION['username'])) {
    header("Location: ../controller/login.php");
    exit(); // Ensure that code execution stops after redirection
}

// Fetch destinations from the packages table
$destinations = [];
$destinationQuery = "SELECT package_id, package_title , package_cost FROM packages";
$destinationResult = $conn->query($destinationQuery);

if ($destinationResult->num_rows > 0) {
    while ($row = $destinationResult->fetch_assoc()) {
        $destinations[] = $row;
    }
}

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

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Retrieve form data
    $user_id = $userId;
    $destination = $_POST["package_id"];
    $status = "Pending"; // Default status for new bookings
    $participants = $_POST["num_people"];
    $package_cost = $_POST["package_cost"];

    // Prepare SQL statement to insert data into the database
    $sql = "INSERT INTO bookings (user_id, package_id, num_people, package_cost, status) 
            VALUES ('$user_id', '$destination', '$participants', '$package_cost', '$status')";

    // Execute the SQL statement
    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Booking successful! Thank you for booking with us.');
                window.location.href = '../index.php'; // Redirect to index.php
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close database connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travel Booking</title>
    <link rel="stylesheet" href="../public/CSS/booking.css">
    <!-- Include SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <li><a href="routes/contact.php">contact</a></li>
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

    <div class="booking">
        <h1>Book Your Trek or Hike</h1>
        <form action="" method="POST">
            

            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username"
                    value="<?php echo isset($_SESSION['username']) ? $_SESSION['username'] : ''; ?>" readonly required>

                <label for="destination">Destination:</label>
                <select id="destination" name="package_id" required onchange="updatePrice()">
                    <?php foreach ($destinations as $destination) { ?>
                    <option value="<?php echo $destination['package_id']; ?>"
                        data-price="<?php echo $destination['package_cost']; ?>">
                        <?php echo $destination['package_title']; ?>
                    </option>
                    <?php } ?>
                </select>

                <label for="participants">Number of Participants:</label>
                <input type="number" id="participants" name="num_people" min="1" value="1" required>

                <label>Total Price :</label>
                <input type="hidden" id="package_cost" name="package_cost" value="">

                <span id="total-price"></span>
            </div>

            <button type="submit">Book Now</button>
        </form>
    </div>

</body>

<script>
    function updatePrice() {
        const destination = document.getElementById('destination');
        const selectedOption = destination.options[destination.selectedIndex];
        const basePrice = parseInt(selectedOption.getAttribute('data-price'), 10);
        const participants = parseInt(document.getElementById('participants').value, 10) || 1;
        const totalPrice = basePrice * participants;
        document.getElementById('total-price').innerText = "Rs: " + totalPrice;
        document.getElementById('package_cost').value = totalPrice;
    }

    // Add an event listener to the participants input field
    document.getElementById('participants').addEventListener('input', updatePrice);
</script>
</html>
<!-- 
<script>
        function updatePrice() {
            const basePrice = 2000;
            const participants = parseInt(document.getElementById('participants').value, 10) || 1;
            const totalPrice = basePrice * participants;
            document.getElementById('total-price').innerText = "$" + totalPrice;
            document.getElementById('package_cost').value = totalPrice;
        }
    </script> -->