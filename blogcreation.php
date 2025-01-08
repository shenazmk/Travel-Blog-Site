<?php
// Include database connection information
require_once("db.php");

// Start session to check user authentication
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Function to sanitize user input
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Initialize variables and error array
$errors = [];
$title = $message = $image = "";
$target_file = "";
$titleErr = $textareaErr = $imgErr = "";

// Get the logged-in user's ID and avatar
$user_id = $_SESSION['user_id'];
$avatar = $_SESSION['avatar'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate title
    if (empty($_POST["title"])) {
        $titleErr = "Title is required: Please type in text.";
    } else {
        $title = test_input($_POST["title"]);
        if (!preg_match("/^.{1,255}$/", $title)) {
            $titleErr = "Title must be between 1 and 255 characters.";
        }
    }

    // Validate message
    if (empty($_POST["message"])) {
        $textareaErr = "Message is required: Please type in text.";
    } else {
        $message = test_input($_POST["message"]);
        if (!preg_match("/^.{1,2000}$/", $message)) {
            $textareaErr = "Message must be between 1 and 2000 characters.";
        }
    }

    // Validate image
    $uploadOk = true;
    if (isset($_FILES['pic']) && $_FILES['pic']['error'] == 0) {
        $imageFileType = strtolower(pathinfo($_FILES['pic']['name'], PATHINFO_EXTENSION));

        // Validate file type
        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            $imgErr = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            $uploadOk = false;
        }

        // Validate file size (max 1MB)
        if ($_FILES['pic']['size'] > 1000000) {
            $imgErr = "File size too large. Maximum size is 1MB.";
            $uploadOk = false;
        }

        $target_dir = "uploads/";
        $target_file_temp = $target_dir . uniqid($user_id . "_") . "." . $imageFileType;
    } else {
        $imgErr = "Image is required, please select an image.";
        $uploadOk = false;
    }

    // If there are no validation errors, proceed with database insertion
    if (empty($titleErr) && empty($textareaErr) && empty($imgErr)) {
        try {
            // Connect to database
            $conn = new PDO($attr, $db_user, $db_pwd, $options);

            // Insert blog post into database
            $query = "INSERT INTO Blogs (user_id, title, content, timestamp, image) VALUES (:user_id, :title, :content, NOW(), :image)";
            $stmt = $conn->prepare($query);
            $stmt->execute([
                ':user_id' => $user_id,
                ':title' => $title,
                ':content' => $message,
                ':image' => basename($target_file_temp)
            ]);

            // Get the ID of the newly created post
            $blog_id = $conn->lastInsertId();
            $target_file = $target_dir . $blog_id . "_" . $user_id . "." . $imageFileType;

            // Handle file upload
            if ($uploadOk && move_uploaded_file($_FILES['pic']['tmp_name'], $target_file_temp)) {
                // Update image path in the database
                $query = "UPDATE Blogs SET image = :image WHERE blog_id = :blog_id";
                $stmt = $conn->prepare($query);
                $stmt->execute([
                    ':image' => basename($target_file),
                    ':blog_id' => $blog_id
                ]);
            } else {
                $imgErr = "Error uploading the image.";
            }

            // Redirect to blog management page after success
            if (empty($imgErr)) {
                header("Location: blogmanagement.php");
                exit();
            }
        } catch (PDOException $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Blog Post</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="grid">
        <!-- header information -->
        <div class="header">
            <img src="images/logo2.jpg" alt="Logo" class="logo">
            <div class="user-area">
                <img src="uploads/<?=$avatar?>" alt="User Avatar">
                <span class="screenname"><?=$_SESSION['screenname']?></span>
                <div class="dropdown">
                    <button class="menu">
                        <img src="images/menu.svg" alt="dropdown menu" />
                    </button>
                    <div class="dropdown-content">
                        <a href="homepage.php">Home Page</a>
                        <a href="blogmanagement.php">Blog Management Page</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <main id="main-center">
            <!-- Display errors if any -->
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?=$error?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form class="auth-form" method="post" enctype="multipart/form-data">
                <input type="text" id="title" name="title" placeholder="Title of Your Post" value="<?php echo htmlspecialchars($title); ?>" />
                
                <div class="right-align">
                    <i class="fa-solid fa-image"></i>
                    <label for="pic"></label>
                    <input type="file" id="pic" name="pic" />
                </div>

                <textarea name="message" id="message" placeholder="Write something amazing..."><?php echo htmlspecialchars($message); ?></textarea>

                <div class="align-right">
                    <i class="fa-solid fa-upload"></i> 
                    <input type="submit" id="submit-button" value="Upload" />
                </div>
            </form>

            <div class="write">
                <h4>Embark on an inspirational journey to write!</h4>
                <p>Enlighten us with your magical and transformative experiences in life.</p>
            </div>
        </main>

        <footer class="footer-space">
            <p class="footer-text">CS 215 Assignment 3 Solution: Group 5</p>
        </footer>
    </div>

    <script src="js/bottom.js"></script>
</body>
</html>
