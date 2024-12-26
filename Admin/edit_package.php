<?php
include '../connect.php'; // Your database connection

// Check if package_id is set in the URL
if (isset($_GET['package_id'])) {
    $package_id = $_GET['package_id'];
    $sql = "SELECT * FROM packages WHERE package_id = $package_id";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $package = mysqli_fetch_assoc($result);
    } else {
        echo "No package found";
        exit;
    }
}

if (isset($_POST['update'])) {
    $package_id = $_POST['package_id'];
    $package_title = $_POST['package_title'];
    $package_description = $_POST['package_description'];
    $package_duration = $_POST['package_duration'];
    $package_cost = $_POST['package_cost'];
    $category = $_POST['category'];

    // Handle Package Image Upload
    if (!empty($_FILES['package_image']['name'])) {
        $target_dir = "../packagesimage/";
        $target_file = $target_dir . basename($_FILES['package_image']['name']);
        
        // Check file type and size if needed
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = array('jpg', 'jpeg', 'png', 'gif');
        $maxFileSize = 5 * 1024 * 1024; // 5 MB
        
        if (!in_array($imageFileType, $allowedTypes)) {
            echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
            exit;
        }
        
        if ($_FILES['package_image']['size'] > $maxFileSize) {
            echo "Sorry, your file is too large.";
            exit;
        }
        
        // Move the uploaded file
        if (move_uploaded_file($_FILES['package_image']['tmp_name'], $target_file)) {
            $package_image = basename($_FILES['package_image']['name']);
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit;
        }
    } else {
        // If no new file uploaded, retain the current image
        $package_image = $package['package_image'];
    }

    // Update the database with the new or existing image name
    $sql = "UPDATE packages SET 
            package_title = '$package_title', 
            package_description = '$package_description', 
            package_duration = '$package_duration', 
            package_cost = '$package_cost', 
            package_image = '$package_image', 
            category = '$category' 
            WHERE package_id = $package_id";

    if (mysqli_query($conn, $sql)) {
        echo '<script>
                alert("Package updated successfully!");
                window.location.href = "test.php";
              </script>';
    } else {
        echo "Error updating package: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Package</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-container {
            width: 25rem;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="number"],
        select,
        textarea {
            font-size: 1rem;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            margin-top: 2rem;
            padding: 10px;
            border: none;
            background: #28a745;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        button:hover {
            background: #218838;
        }
    </style>
</head>

<body>
    <div class="form-container">
        <form action="edit_package.php?package_id=<?php echo $package['package_id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="package_id" value="<?php echo $package['package_id']; ?>">

            <label for="package_title">Package Title</label>
            <input type="text" name="package_title" value="<?php echo $package['package_title']; ?>" required>

            <label for="package_description">Package Description</label>
            <textarea name="package_description" required><?php echo $package['package_description']; ?></textarea>

            <label for="package_duration">Package Duration</label>
            <input type="text" name="package_duration" value="<?php echo $package['package_duration']; ?>" required>

            <label for="package_cost">Package Cost</label>
            <input type="number" name="package_cost" value="<?php echo $package['package_cost']; ?>" required>

            <label for="package_image">Package Image</label>
            <input type="file" name="package_image">
            <?php if (!empty($package['package_image'])): ?>
                <img src="../packagesimage/<?php echo $package['package_image']; ?>" alt="Current Package Image"
                    style="max-width: 200px; max-height: 200px;">
            <?php endif; ?>

            <label for="category">Category</label>
            <select name="category" required>
                <option value="hiking tours" <?php if ($package['category'] == 'hiking tours') echo 'selected'; ?>>Hiking Tours</option>
                <option value="jungle safari" <?php if ($package['category'] == 'jungle safari') echo 'selected'; ?>>Jungle Safari</option>
                <option value="rafting" <?php if ($package['category'] == 'rafting') echo 'selected'; ?>>Rafting</option>
                <option value="camping" <?php if ($package['category'] == 'camping') echo 'selected'; ?>>Camping</option>
            </select>

            <button type="submit" name="update">Update Package</button>
        </form>
    </div>
</body>

</html>
