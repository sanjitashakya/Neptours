<?php
require '../connect.php'; // Adjust this path based on your file structure and database connection method

// Function to handle errors
function handleError($message)
{
    echo "<script>alert('$message'); window.location.href='packages.php';</script>";
    exit;
}

// Handle deletion of package
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM packages WHERE package_id = ?";
    if ($stmt = $conn->prepare($delete_query)) {
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            handleError("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
        handleError("Package deleted successfully");
    } else {
        handleError("Error preparing statement: " . $conn->error);
    }
}

/// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize inputs
    $package_title = isset($_POST['package_title']) ? htmlspecialchars(trim($_POST['package_title'])) : '';
    $package_description = isset($_POST['package_description']) ? htmlspecialchars(trim($_POST['package_description'])) : '';
    $package_duration = isset($_POST['package_duration']) ? intval($_POST['package_duration']) : '';

    // Handle file upload
    $imagePaths = [];
    $targetDir = "uploads/";

    // Create the upload directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $filename = basename($_FILES['images']['name'][$key]);
            $targetFile = $targetDir . $filename;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            // Validate file type
            $validExtensions = array('jpg', 'jpeg', 'png');
            if (!in_array($fileType, $validExtensions)) {
                handleError("Invalid file type: " . $filename);
            }

            if (move_uploaded_file($tmp_name, $targetFile)) {
                $imagePaths[] = $filename; // Store only the filename in array
            } else {
                handleError("Error uploading file: " . $filename);
            }
        }
    } else {
        handleError("No images uploaded.");
    }

    // Convert array of image filenames to JSON string
    $images_json = json_encode($imagePaths);

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO packages (package_title, package_description, package_duration, package_image) VALUES (?, ?, ?, ?)");
    if (!$stmt) {
        handleError("Error preparing statement: " . $conn->error);
    }

    $stmt->bind_param("ssis", $package_title, $package_description, $package_duration, $images_json);

    // Execute the statement
    if ($stmt->execute()) {
        handleError("New record created successfully");
    } else {
        handleError("Error: " . $stmt->error);
    }

    // Close statement
    $stmt->close();
}


// Query to fetch popular packages
$query_popular = 'SELECT * FROM popularpackage LIMIT 6';
$result_popular = mysqli_query($conn, $query_popular);
$popular_packages = [];

if ($result_popular) {
    while ($row = mysqli_fetch_assoc($result_popular)) {
        $popular_packages[] = $row;
    }
} else {
    handleError("Error fetching popular packages: " . mysqli_error($conn));
}

// Query to fetch all packages
$query_all = 'SELECT * FROM packages';
$result_all = mysqli_query($conn, $query_all);
$all_packages = [];

if ($result_all) {
    while ($row = mysqli_fetch_assoc($result_all)) {
        $all_packages[] = $row;
    }
} else {
    handleError("Error fetching all packages: " . mysqli_error($conn));
}

// Close connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Packages</title>
    <link rel="stylesheet" href="packages.css">
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
                <li><a href="packages.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'active' : ''; ?>">Packages</a>
                </li>
                <li><a href="../login.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="packages container">
        <div class="popularPack">
            <?php foreach ($popular_packages as $package): ?>
                <a href="#" class="card">
                    <div class="cimg">
                        <img src="<?php echo $package['pimage']; ?>" alt="">
                    </div>
                    <div class="card-body">
                        <h1 class="card-title"><?php echo $package['package_name']; ?></h1>
                    </div>
                    <div class="card-buttons">
                        <button class="edit-button">Edit</button>
                        <button class="delete-button">Delete</button>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>


        <div class="allpack" style="display: none;">
            <div class="package-list">
                <h2>Package List</h2>
                <button class="button addpack">Add Packages</button>
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Package Title</th>
                            <th>Package Image</th>
                            <th>Package Description</th>
                            <th>Package Duration</th>
                            <th>Package Creator</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_packages as $package): ?>
                            <tr>
                                <td><?php echo $package['package_id']; ?></td>
                                <td><?php echo $package['package_title']; ?></td>
                                <td>
                                    <?php
                                    $images = json_decode($package['package_image'], true);
                                    if (!empty($images)) {
                                        foreach ($images as $image) {
                                            echo "<img src='image/{$image}' alt='{$package['package_title']}' style='width:100px;height:100px;'>";
                                        }
                                    }
                                    ?>
                                </td>
                                <td><?php echo $package['package_description']; ?></td>
                                <td><?php echo $package['package_duration']; ?> Days</td>
                                <td><?php echo $package['package_creator']; ?></td>
                                <td>
                                    <a href="edit_package.php?id=<?php echo $package['package_id']; ?>"
                                        class="button edit">Edit</a>
                                    <a href="packages.php?delete=<?php echo $package['package_id']; ?>"
                                        class="button delete"
                                        onclick="return confirm('Are you sure you want to delete this package?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>
        </div>

        <div class="btn-field">
            <button type="button" class="packbutton popular active">Popular Tour</button>
            <button type="button" class="packbutton packall">Packages</button>
        </div>
    </section>

    <!-- Pop up to add package-->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <form id="loginForm" action="packages.php" method="post" enctype="multipart/form-data">
                <div id="imageContainer">
                    <label for="packageImages">Package Images:</label>
                    <input type="file" name="images[]" accept=".jpg, .jpeg, .png" required multiple>
                </div>

                <label for="packageName">Package Title:</label>
                <input type="text" id="packageName" name="package_title" required>

                <label for="description">Description:</label>
                <textarea id="description" name="package_description" required></textarea>

                <label for="duration">Duration:</label>
                <input type="number" id="duration" name="package_duration" required>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <script>
        document.querySelector('.popular').addEventListener('click', function () {
            document.querySelector('.popularPack').style.display = 'block';
            document.querySelector('.allpack').style.display = 'none';
        });

        document.querySelector('.packall').addEventListener('click', function () {
            document.querySelector('.popularPack').style.display = 'none';
            document.querySelector('.allpack').style.display = 'block';
        });

        document.querySelector('.addpack').addEventListener('click', function () {
            document.getElementById('myModal').style.display = 'block';
        });

        document.querySelector('.close').addEventListener('click', function () {
            document.getElementById('myModal').style.display = 'none';
        });

        window.onclick = function (event) {
            if (event.target == document.getElementById('myModal')) {
                document.getElementById('myModal').style.display = 'none';
            }
        }
    </script>
</body>

</html>