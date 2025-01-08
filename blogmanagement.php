<?php
session_start();

require_once("db.php");

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Get the logged-in user's ID
$user_id = $_SESSION['user_id'];

// Connect to the database and fetch posts for the authenticated user
try {
    $db = new PDO($attr, $db_user, $db_pwd, $options);

    // Prepare SQL query to fetch posts by user_id

    $query = "SELECT blog_id, title, content, timestamp, image FROM Blogs WHERE user_id = '$user_id' ORDER BY timestamp DESC";
    $result = $db->query($query);

    if (!$result) {
        // query has an error
        $errors["Database Error"] = "Could not retrieve blogs for this user.";
    } else {
        // Fetch all posts for the logged-in user
        $posts = $result->fetchAll();
    }
    $query2 = "SELECT screenname, avatar FROM Users WHERE user_id = '$user_id'";
    $result2 = $db->query($query2);

    if (!$result2) {
        // query has an error
        $errors["Database Error"] = "Could not retrieve user information.";
    } else {
        $userinfo = $result2->fetch();
        $screenname = $userinfo['screenname'];
        $avatar = $userinfo['avatar'];
    }  

} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

$db = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Management Page</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <div class="header">
        <img src="images/logo2.jpg" alt="Logo" class="logo">
        <p class="header-title"><?=$screenname?>'s Posts</p>

        <div class="user-area">
            <img src="uploads/<?=$avatar?>" alt="User Avatar">
            <span class="screenname"><?=$screenname?></span>

            <div class="dropdown">
                <button class="menu">
                    <img src="images/menu.svg" alt="dropdown menu" />
                </button>
                <div class="dropdown-content">
                    <a href="homepage.php">Home Page</a>
                    <a href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <section class="posts-section">
        <div class="align-right">
            <i class="fa-solid fa-plus"></i>
            <input class="newpost" type="submit" value="New Post" onclick="location.href='blogcreation.php';" />
        </div>

        <?php if (!empty($posts)) { ?>
    <!-- Display posts dynamically -->
    <?php foreach ($posts as $post) { ?>
        <div class="post">
            <div class="post-header">
                <div class="avatar">
                    <img src="uploads/<?=$avatar?>" alt="Avatar">
                </div>
                <div class="screenname"><?=$screenname?></div>
                <div class="date-time"><?= htmlspecialchars($post['timestamp']); ?></div>
            </div>

            <div class="post-details">
                <div class="post-content">
                    <img class="post-image" src="uploads/<?=htmlspecialchars($post['image']);?>" alt="Post Image">
                    <h3><?= htmlspecialchars($post['title']) ?></h3>
                    <p><?= htmlspecialchars(substr($post['content'], 0, 100)) ?>... <a href="blogpost.php?blog_id=<?= $post['blog_id']; ?>">See more</a></p>
                </div>
            </div>

            <div class="comments-bm">
                <h2>Comments</h2>
                <!-- Assuming you have comments related to each post -->
                <div class="comment">
                    <img src="uploads/1.jpg" alt="John Mann Avatar">
                    <div>
                        <div class="date-time-comment-bm">
                            <h4>Omar Ali</h4>
                            • 05-11-2023 | 8:00 PM
                        </div>
                        <p class="white-font">Looking forward to your next post!</p>
                        <div class="icons">
                            <div class="votes">
                                <img src="images/arrow-up.svg" alt="up-vote" />
                                <p class="white-font">26</p>
                            </div>
                            <div class="votes">
                                <img src="images/arrow-down.svg" alt="down-vote" />
                                <p class="white-font">8</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
<?php } else { ?>
    <p>You have no blogs. Create a blog now. You could inspire someone today!</p>
<?php } ?>
    </section>

    <footer class="footer-space">
        <p class="footer-text">CS 215 Assignment 3 Solution: Group 5</p>
    </footer>
</body>

</html>
