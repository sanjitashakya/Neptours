<?php
require '../connect.php';
session_start();

    // Check if user is not logged in, redirect to login.php
    if (!isset($_SESSION['admin_name'])) {
      header("Location: index.php");
      exit(); // Ensure that code execution stops after redirection
    }

    
// Handle deletion of popular package
if (isset($_GET['remove_popular'])) {
    $id = $_GET['remove_popular'];
    $update_query = "UPDATE packages SET is_popular = 0 WHERE package_id = ?";
    if ($stmt = $conn->prepare($update_query)) {
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            echo("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
    } else {
        echo("Error preparing statement: " . $conn->error);
    }
}



// Handle marking a package as popular
if (isset($_GET['make_popular'])) {
    $id = $_GET['make_popular'];
    $count_query = "SELECT COUNT(*) as popular_count FROM packages WHERE is_popular = 1";
    $result = $conn->query($count_query);

    if ($result) {
        $row = $result->fetch_assoc();
        $popular_count = $row['popular_count'];

        if ($popular_count >= 6) {
            echo "You have reached the limit of 6 popular packages.";
        } else {
            $update_query = "UPDATE packages SET is_popular = 1 WHERE package_id = ?";
            if ($stmt = $conn->prepare($update_query)) {
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) {
                    echo("Error executing statement: " . $stmt->error);
                } else {
                    echo '<script>alert("Package marked as popular successfully."); window.location.refresh();</script>';
                }
                $stmt->close();
            } else {
                echo("Error preparing statement: " . $conn->error);
            }
        }
    } else {
        echo("Error executing count query: " . $conn->error);
    }
}



// Handle deletion of Allpackage
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

       // Delete related bookings first
       $delete_bookings_query = "DELETE FROM bookings WHERE package_id = ?";
       if ($stmt = $conn->prepare($delete_bookings_query)) {
           $stmt->bind_param("i", $id);
           if (!$stmt->execute()) {
               echo("Error executing delete bookings statement: " . $stmt->error);
               exit; // Exit or handle error as needed
           }
           $stmt->close();
       } else {
           echo("Error preparing delete bookings statement: " . $conn->error);
           exit; // Exit or handle error as needed
       }

    $delete_query = "DELETE FROM packages WHERE package_id = ?";
    if ($stmt = $conn->prepare($delete_query)) {
        $stmt->bind_param("i", $id);
        if (!$stmt->execute()) {
            echo("Error executing statement: " . $stmt->error);
        }
        $stmt->close();
    } else {
        echo("Error preparing statement: " . $conn->error);
    }
}

// Handle addition of Allpackages
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $package_title = $_POST['package_title'];
    $package_description = $_POST['package_description'];
    $package_duration = $_POST['package_duration'];
    $package_cost = $_POST['package_cost'];
    $category = $_POST['category'];

    // Handle file upload
    $targetDir = "../packagesimage/"; // Set your target directory
    $imagePath = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $filename = basename($_FILES['image']['name']);
        $targetFile = $targetDir . $filename;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Move the uploaded file
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $imagePath = $filename;
        } else {
            echo("Error uploading file: " . $filename);
            exit;
        }
    }

    $stmt = $conn->prepare("INSERT INTO packages (package_title, package_description, package_duration, package_image, category,package_cost) VALUES (?, ?, ?, ?, ?,?)");
    $stmt->bind_param("ssisss", $package_title, $package_description, $package_duration, $imagePath, $category, $package_cost);

    if ($stmt->execute()) {
        echo '<script>
                Swal.fire("Success!", "Package added successfully!", "success").then(function() {
                    window.location.href = window.location.href;
                });
            </script>';
    } else {
        echo("Error: " . $stmt->error);
    }

    $stmt->close();
}


// Fetch all packages
$query_all = 'SELECT * FROM packages';
$result_all = mysqli_query($conn, $query_all);
$all_packages = [];

if ($result_all) {
    while ($row = mysqli_fetch_assoc($result_all)) {
        $all_packages[] = $row;
    }
} else {
    echo("Error fetching all packages: " . mysqli_error($conn));
}

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
                <li><a href="test.php"
                        class="<?php echo basename($_SERVER['PHP_SELF']) == 'test.php' ? 'active' : ''; ?>">Packages</a>
                </li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <section class="packages container">
        <div class="popularpack">
        <?php
require '../connect.php';

// Fetch popular packages
$sql = "SELECT * FROM packages WHERE is_popular = 1 LIMIT 6";
$result = $conn->query($sql);
$popular_packages = [];

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $popular_packages[] = $row;
    }
}

if (!empty($popular_packages)) {
    foreach ($popular_packages as $row) {
        // Assuming only one image per package
        $image = $row["package_image"]; // Assuming the column name is package_image
        echo '<div class="card">';
        echo "<img src='../packagesimage/{$image}' alt='{$row["package_title"]}' class='card-image'>";
        echo '<h1 class="card-title">' . $row["package_title"] . '</h1>';
        echo '<div class="card-buttons">';
        echo '<a href="test.php?remove_popular=' . $row["package_id"] . '" class="delete-button">Remove from Popular</a>';
        echo '</div>';
        echo '</div>';
    }

    $remainingCards = 6 - count($popular_packages);
    for ($i = 0; $i < $remainingCards; $i++) {
        echo '<div class="add-popular" id="addPackageButton"><a href="#" onclick="openModal(\'myModal\')"><i class="fas fa-plus"></i> Add Package</a></div>';
    }
} else {
    // If no popular packages found, show placeholders
    for ($i = 0; $i < 6; $i++) {
        echo '<div class="add-popular" id="addPackageButton"><a href="#" onclick="openModal(\'myModal\')"><i class="fas fa-plus"></i> Add Package</a></div>';
    }
}
?>
        </div>

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
                            <th>Package Cost</th>
                            <th>Category</th>
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
                                    if (!empty($package['package_image'])) {
                                        echo "<img src='../packagesimage/{$package['package_image']}' alt='{$package['package_title']}' style='width:100px;height:100px;'>";
                                    }
                                    ?>
                                </td>
                                <td><?php echo $package['package_description']; ?></td>                              
                                <td><?php echo $package['package_duration']; ?> Days</td>
                                <td><?php echo $package['package_cost']; ?>Rs </td>
                                <td><?php echo $package['category']; ?></td>
                                <td>
                                    <a href="edit_package.php?package_id=<?php echo $package['package_id']; ?>"
                                        class="button edit">Edit</a>
                                    <a href="test.php?delete=<?php echo $package['package_id']; ?>" class="button delete"
                                        onclick="return confirm('Are you sure you want to delete this package?');">Delete</a>
                                    <a href="test.php?make_popular=<?php echo $package['package_id']; ?>"
                                        class="button select-popular">Select as Popular</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div id="myModalAll" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal('myModalAll')">x</span>
                    <form id="loginForm" action="test.php" method="post" enctype="multipart/form-data">
                        <label for="packageName">Package Title:</label>
                        <input type="text" id="packageName" name="package_title" required>
                        <label for="description">Description:</label>
                        <textarea id="description" name="package_description" required></textarea>
                        <label for="duration">Duration:</label>
                        <input type="number" id="duration" name="package_duration" required>

                        <label for="pacakgeCost">pacakage Cost:</label>
                        <input type="number" id="cost" name="package_cost" required>
                        <div id="imageContainer">
                            <label for="packageImages">Package Images:</label>
                            <input type="file" name="image" accept=".jpg, .jpeg, .png" required>
                        </div>
                        <label for="category">Category:</label>
                        <select id="category" name="category" required>
                            <option value="Hiking">Hiking</option>
                            <option value="Tours">Tours</option>
                            <option value="Junglesafari">Jungle Safari</option>
                            <option value="Rafting">Rafting</option>
                        </select>
                        <button type="submit" class="buton">Submit</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="btn-field">
            <button type="button" class="packbutton popular">Popular Tour</button>
            <button type="button" class="packbutton packall">Packages</button>
        </div>
    </section>

    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/sweetalert2.min.js"></script>
    <script>
        function confirmDelete() {
            return confirm("Are you sure you want to delete this package?");
        }
    </script>
</body>

</html>