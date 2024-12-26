<?php
require '../connect.php'; 


// Handle deletion of Allpackage
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_query = "DELETE FROM packages WHERE package_id = ?";
    if ($stmt = $conn->prepare($delete_query)) {
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            handleError("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
        echo("Package deleted successfully");
    } else {
        handleError("Error preparing statement: " . $conn->error);
    }
}

// Handle addition of Allpackages
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
        handleError("Error: " . $stmt->error);
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

// // Close connection
mysqli_close($conn);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
                <li><a href="finalpackages.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'finalpackages.php' ? 'active' : ''; ?>">Packages</a>
                </li>
                <li><a href="../login.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="packages container">
        <div class="popularpack">
            <?php
            require '../connect.php';


            // Handle delete popular package request
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
                $delete_id = $_POST['delete_id'];

                // Delete the package from the database
                $sql_delete = "DELETE FROM popularpackage WHERE id =?";
                $stmt_delete = $conn->prepare($sql_delete);
                $stmt_delete->bind_param("i", $delete_id);

                if ($stmt_delete->execute()) {
                    // Success message
                    echo '<p>Package deleted successfully!</p>';
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    exit;
                } else {
                    // Error message
                    echo '<p>Failed to delete package.</p>';
                    error_log("Failed to delete package: " . $stmt_delete->error);
                }
            }

            // Handle add popular package request
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['package_name'])) {
                $target_dir = "../packagesimage/";
                $target_file = $target_dir . basename($_FILES["pimage"]["name"]);

                // Attempt to move the uploaded file to the designated folder
                if (move_uploaded_file($_FILES["pimage"]["tmp_name"], $target_file)) {
                    $package_name = $_POST["package_name"];
                    $description = $_POST["pdescription"];
                    $pimage = basename($_FILES["pimage"]["name"]);

                    // Insert data into the database
                    $sql = "INSERT INTO popularpackage (package_name, pdescription, pimage) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sss", $package_name, $description, $pimage);
                    if ($stmt->execute()) {
                        // Success message
                        echo '
                <script>
                    Swal.fire("Success!", "Package added successfully!", "success").then(function() {
                        window.location.href = window.location.href; // Refresh the page
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
                }
            }

            // Fetch POPULARPACKAGES from the database
            
            $sql = "SELECT * FROM popularpackage LIMIT 6";
            $result = $conn->query($sql);
            $popular_packages = [];

            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $popular_packages[] = $row;
                }
            }

            // Display data in cards
            if (!empty($popular_packages)) {
                foreach ($popular_packages as $row) {
                    echo '<div class="card">';
                    echo '<img src="' . $row["pimage"] . '" alt="' . $row["package_name"] . '">';
                    echo '<h1 class="card-title">' . $row["package_name"] . '</h1>';
                    echo '<div class="card-buttons">';
                    echo '<button class="edit-button">Edit</button>';

                    // Add confirmation dialog to delete button
                    echo '<form method="POST" style="display:inline-block;" onsubmit="return confirmDelete()">';
                    echo '<input type="hidden" name="delete_id" value="' . $row["id"] . '">';
                    echo '<input type="hidden" name="confirm_delete" id="confirm_delete" value="">'; // Hidden input for confirmation
                    echo '<button type="submit" class="delete-button">Delete</button>';
                    echo '</form>';

                    echo '</div>';
                    echo '</div>';
                }

                // Display "Add Package" button for remaining cards
                $remainingCards = 6 - count($popular_packages);
                for ($i = 0; $i < $remainingCards; $i++) {
                    echo '<div class="add-popular" id="addPackageButton"><a href="#" onclick="openModal(\'myModal\')"><i class="fas fa-plus"></i> Add Package</a></div>';
                }
            } else {
                // Show 6 "Add Package" buttons if no data is found
                for ($i = 0; $i < 6; $i++) {
                    echo '<div class="add-popular" id="addPackageButton"><a href="#" onclick="openModal(\'myModal\')"><i class="fas fa-plus"></i> Add Package</a></div>';
                }
            }
            ?>



            <!-- The Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('myModal')">&times;</span>
                    <h2>Add Popular Package</h2>
                    <form action="#" method="POST" enctype="multipart/form-data">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="package_name" required>
                        <label for="description">Description</label>
                        <textarea id="description" name="pdescription" required></textarea>
                        <input type="file" id="fileInput" name="pimage" required>
                        <button type="submit" class="button-submit">Submit</button>
                    </form>
                </div>
            </div>

            <!-- JavaScript function for confirmation dialog -->

        </div>

        <!-- all package section  -->

        <div class="allpack">
            <div class="package-list">
                <h2>Package Lists</h2>
                <button class="button addpack" href="#" onclick="openModal('myModalAll')">Add Packages</button>

                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Package Title</th>
                            <th>Package Image</th>
                            <th>Package Description</th>
                            <th>Package Duration</th>
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
                                <td>
                                    <a href="edit_package.php?id=<?php echo $package['package_id']; ?>"
                                        class="button edit">Edit</a>
                                    <a href="finalpackages.php?delete=<?php echo $package['package_id']; ?>"
                                        class="button delete"
                                        onclick="return confirm('Are you sure you want to delete this package?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

            <!-- Pop up to add package-->
            <div id="myModalAll" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('myModalAll')">&times;</span>
                    <form id="loginForm" action="finalpackages.php" method="post" enctype="multipart/form-data">

                        <label for="packageName">Package Title:</label>
                        <input type="text" id="packageName" name="package_title" required>

                        <label for="description">Description:</label>
                        <textarea id="description" name="package_description" required></textarea>

                        <label for="duration">Duration:</label>
                        <input type="number" id="duration" name="package_duration" required>

                        <div id="imageContainer">
                            <label for="packageImages">Package Images:</label>
                            <input type="file" name="package_image" accept=".jpg, .jpeg, .png" required multiple>
                        </div>

                        <button type="submit" class="button">Submit</button>
                    </form>
                </div>
            </div>


        </div>



        <div class="btn-field">
            <button type="button" class="packbutton popular">Popular Tour</button>
            <button type="button" class="packbutton packall">Packages</button>
        </div>
    </section>

</body>
<script src="main.js"></script>

<script>
    function confirmDelete() {
        // Show confirmation dialog
        var result = confirm("Are you sure you want to delete this package?");
        if (result) {
            document.getElementById('confirm_delete').value = 'yes'; // Set confirmation value
            return true; // Allow form submission
        } else {
            return false; // Cancel form submission
        }
    }
</script>

</html>