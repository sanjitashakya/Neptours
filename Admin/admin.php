

<!DOCTYPE html>
<html>

<head>
	<title>Admin Dashboard</title>
	<link rel="stylesheet" href="admin.css">
</head>

<body>
	<header>
		<h1>NEPTOURS</h1>
		<nav>
			<ul>
				<li><a href="admin.php"
						class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>">Dashboard</a>
				</li>
				<li><a href="bookings.php">Bookings</a></li>
				<li><a href="user.php">Users</a></li>
				<li><a href="test.php">Packages</a></li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
		</nav>
	</header>

	<div class="content">
		<a href="bookings.php">
			<div class="box blue">
				<h1>Total Bookings</h1>
				<?php
				require '../connect.php';

				// Query to retrieve total bookings
				$sql = "SELECT COUNT(*) as total_bookings FROM bookings";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					// Output data of each row
					while ($row = $result->fetch_assoc()) {
						echo "<p>" . $row["total_bookings"] . "</p>";
					}
				} else {
					echo "<p>No bookings found.</p>";
				}

				$conn->close();
				?>
			</div>
		</a>

		<a href="user.php">
			<div class="box green">
				<h1>Total Users</h1>
				<?php
				require '../connect.php';

				// Query to retrieve total users
				$sql = "SELECT COUNT(*) as total_users FROM user";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					// Output data of each row
					while ($row = $result->fetch_assoc()) {
						echo "<p>" . $row["total_users"] . "</p>";
					}
				} else {
					echo "<p>No users found.</p>";
				}

				$conn->close();
				?>
			</div>
		</a>
	</div>

	<div class="container">
		<a href="test.php">
			<div class="box orange"  >
				<h1>Total Packages</h1>
				<?php
				require '../connect.php';

				// Query to retrieve total packages
				$sql = "SELECT COUNT(*) as total_packages FROM packages";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					// Output data of each row
					while ($row = $result->fetch_assoc()) {
						echo "<p>" . $row["total_packages"] . "</p>";
					}
				} else {
					echo "<p>No packages found.</p>";
				}

				$conn->close();
				?>
			</div>
		</a>

		<a href="bookings.php">
			<div class="box purple">
				<h1>Pending Packages</h1>
				<?php
				require '../connect.php';

				// Query to retrieve total pending packages
				$sql = "SELECT COUNT(*) as pending_packages FROM bookings WHERE status = 'pending'";
				$result = $conn->query($sql);

				if ($result->num_rows > 0) {
					// Output data of each row
					while ($row = $result->fetch_assoc()) {
						echo "<p>" . $row["pending_packages"] . "</p>";
					}
				} else {
					echo "<p>No pending packages found.</p>";
				}

				$conn->close();
				?>
			</div>
		</a>
	</div>

</body>

<?php include ('footer.php') ?>

</html>
