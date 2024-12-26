<?php
require '../connect.php';


// Handle deletion of ALLpackage
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM packages WHERE package_id = ?";
    if ($stmt = $conn->prepare($delete_query)) {
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            echo ("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
        echo ("Package deleted successfully");
    } else {
        echo ("Error preparing statement: " . $conn->error);
    }
}

/// Handle form submission OF ALLPACKAGE
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize inputs
    $package_title = isset($_POST['package_title']) ? htmlspecialchars(trim($_POST['package_title'])) : '';
    $package_description = isset($_POST['package_description']) ? htmlspecialchars(trim($_POST['package_description'])) : '';
    $package_duration = isset($_POST['package_duration']) ? intval($_POST['package_duration']) : '';

    // Handle file upload
    $imagePaths = [];
    $targetDir = "../packagesimage/";

    // Create the upload directory if it doesn't exist
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    if (isset($_FILES['images'])) {
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            $filename = basename($_FILES['images']['name'][$key]);
            $targetFile = $targetDir . $filename;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            if (move_uploaded_file($tmp_name, $targetFile)) {
                $imagePaths[] = $filename; // Store only the filename in array
            } else {
                handleError("Error uploading file: " . $filename);
            }
        }
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
        echo '
        <script>
            Swal.fire("Success!", "Package added successfully!", "success").then(function() {
                window.location.href = window.location.href; // Refresh the page
            });
        </script>';
    } else {
        echo ("Error: " . $stmt->error);
    }

    // Close statement
    $stmt->close();
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
    <link rel="stylesheet" href="finalpack.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
        <div class="popularpack">

            <!-- php ocde to handle popular packages -->

            <?php
            require '../connect.php';

            
// Handle add popular package request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['package_name'])) {
    $target_dir = "image/";
    $target_file = $target_dir . basename($_FILES["pimage"]["name"]);

    // Attempt to move the uploaded file to the designated folder
    if (move_uploaded_file($_FILES["pimage"]["tmp_name"], $target_file)) {
        $package_name = $_POST["package_name"];
        $description = $_POST["pdescription"];
        $pimage = basename($_FILES["pimage"]["name"]);

        // Insert data into the popularpackage table
        $sql = "INSERT INTO popularpackage (package_name, pdescription, pimage) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $package_name, $description, $pimage);

        if ($stmt->execute()) {
            // Success message
            echo '
            <script>
                Swal.fire("Success!", "Package added successfully!", "success").then(function() {
                    location.reload(); // Reload the current page
                });
            </script>';
        } else {
            // Error message
            echo '
            <script>
                Swal.fire("Error!", "Failed to add package.", "error");
            </script>';
            // Log the error
            error_log("Failed to add package: " . $stmt->error);
        }
    } else {
        // Error message if file upload fails
        echo '
        <script>
            Swal.fire("Error!", "Failed to upload image.", "error");
        </script>';
    }
}

// Handle delete popular package request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Delete the package from the database
    $sql_delete = "DELETE FROM popularpackage WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $delete_id);

    if ($stmt_delete->execute()) {
        // Success message
        echo '<p>Package deleted successfully!</p>';
        header('Location: ' . $_SERVER['PHP_SELF']); // Refresh the page after deletion
        exit;
    } else {
        // Error message
        echo '<p>Failed to delete package.</p>';
        error_log("Failed to delete package: " . $stmt_delete->error);
    }
}

// Fetch data for popular packages
$query_popular = 'SELECT * FROM popularpackage LIMIT 6';
$result_popular = mysqli_query($conn, $query_popular);
$popular_packages = [];

if ($result_popular) {
    while ($row = mysqli_fetch_assoc($result_popular)) {
        $popular_packages[] = $row;
    }
} else {
    echo("Error fetching popular packages: " . mysqli_error($conn));
}



            $sql = "SELECT * FROM popularpackage LIMIT 6";
            $result = $conn->query($sql);

            // Display data in cards
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card">';
                    echo '<img src="image/' . $row["pimage"] . '" alt="' . $row["package_name"] . '">';
                    echo '<h1 class="card-title">' . $row["package_name"] . '</h1>';
                    echo '<div class="card-buttons">';
                    echo '<button class="edit-button">Edit</button>';
                    echo '<form method="POST" style="display:inline-block;" onsubmit="return confirmDelete()">';
                    echo '<input type="hidden" name="delete_id" value="' . $row["id"] . '">';
                    echo '<button type="submit" class="delete-button">Delete</button>';
                    echo '</form>';
                    echo '</div>';
                    echo '</div>';
                }

                $remainingCards = 6 - $result->num_rows;
                for ($i = 0; $i < $remainingCards; $i++) {
                    echo '<div class="add-popular" id="addPackageButton"><a href="#" onclick="openModal(\'myModal\')"><i class="fas fa-plus"></i> Add Package</a></div>';
                }
            } else {
                for ($i = 0; $i < 6; $i++) {
                    echo '<div class="add-popular" id="addPackageButton"><a href="#" onclick="openModal(\'myModal\')"><i class="fas fa-plus"></i> Add Package</a></div>';
                }
            }

            $conn->close();
            ?>

            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('myModal')">&times;</span>
                    <h2>Add Popular Package</h2>
                    <form action="#" method="POST" enctype="multipart/form-data">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="package_name" required>
                        <label for="description">Description</label>
                        <textarea id="description" name="pdescription" required></textarea>
                        <input type="file" id="fileInput" name="pimage" accept=".jpg, .jpeg, .png" required>
                        <button type="submit" class="button-submit">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="allpack" style="display: none;">
            <div class="package-list">
                <h2>Package List</h2>
                <button class="button addpack" onclick="openModal('myModalAll')">Add Packages</button>
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
                                            echo "<img src='../packagesimage/{$image}' alt='{$package['package_title']}' style='width:100px;height:100px;'>";
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

    <div id="myModalAll" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('myModalAll')">&times;</span>
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

    <script src="main.js"></script>
</body>

</html>