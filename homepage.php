<?php
session_start();
require_once("db.php");


// Check if the user is logged in
if (isset($_SESSION["user_id"])) {
    // If the user is logged in, fetch the 20 most recent posts
    $postsLimit = 20;
} else {
    // If the user is not logged in, fetch only the 5 most recent posts
    $postsLimit = 5;
}

try {
    $db = new PDO($attr, $db_user, $db_pwd, $options);
} catch (PDOException $e) {
    throw new PDOException($e->getMessage(), (int)$e->getCode());
}

// Query to fetch the most recent blog posts, limiting to the appropriate number
$sql = "
    SELECT Blogs.blog_id, Blogs.title, Blogs.content, Blogs.timestamp, Users.screenname, Users.avatar, 
           (SELECT COUNT(*) FROM Comments WHERE Comments.blog_id = Blogs.blog_id) AS comment_count,
           Blogs.image  -- Assuming Blogs table has an 'image' field for post images
    FROM Blogs
    JOIN Users ON Blogs.user_id = Users.user_id
    ORDER BY Blogs.timestamp DESC
    LIMIT :postsLimit
";

// Prepare and execute the query
$stmt = $db->prepare($sql);

// Bind the postsLimit value to the query
$stmt->bindParam(':postsLimit', $postsLimit, PDO::PARAM_INT);

// Execute the query
$stmt->execute();

// Fetch the results
$recentBlogs = $stmt->fetchAll();

// Get user information from session if logged in
$screenname = isset($_SESSION["screenname"]) ? $_SESSION["screenname"] : null;
$avatar = isset($_SESSION["avatar"]) ? $_SESSION["avatar"] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" 
          integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" 
          crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <img src="images/logo2.jpg" alt="Logo" class="logo">
        <?php if ($screenname): ?>
            <p class="header-title">Welcome, <?=$screenname?></p>
        <?php endif; ?>

        <div class="user-area">
            <?php if ($screenname): ?>
                <img src="uploads/<?=$avatar?>" alt="Avatar">
                <span class="screenname"><?=$screenname?></span>
                <div class="dropdown">
                    <button class="menu">
                        <img src="images/menu.svg" alt="dropdown menu" />
                    </button>
                    <div class="dropdown-content">
                        <a href="blogmanagement.php">Blog Management Page</a>
                        <a href="blogcreation.php">Create a Post</a>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <p class="header-title">Please <a href="login.php">Login</a> to see more posts</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Posts Section -->
    <section id="posts-section" class="posts-section">
        <?php if (empty($recentBlogs)): ?>
            <p>No posts available.</p>
        <?php else: ?>
            <?php foreach ($recentBlogs as $post): ?>
            <div class="post">
                <div class="post-header">
                    <div class="avatar">
                        <img src="uploads/<?php echo htmlspecialchars($post['avatar']); ?>" alt="Avatar">
                    </div>
                    <div class="screenname"><?php echo htmlspecialchars($post['screenname']); ?></div>
                    <div class="date-time"><?php echo date("d-m-Y | h:i A", strtotime($post['timestamp'])); ?></div>
                </div>
                <div class="post-details">
                    <div class="post-content">
                        <img class="post-images" src="images/<?php echo htmlspecialchars($post['image']); ?>" alt="Post Image">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <p><?php echo (strlen($post['content']) > 150) ? substr(htmlspecialchars($post['content']), 0, 150) . "..." : htmlspecialchars($post['content']); ?></p>
                        <div class="white-font">
                            <p><i class="fa-solid fa-message"></i> <?php echo $post['comment_count']; ?> </p>
                        </div>
                        <a href="blogpost.php?id=<?php echo $post['blog_id']; ?>">See more</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>

    <footer class="footer-space">
        <p class="footer-text">CS 215 Assignment 5 Solution: Group 5</p>
    </footer>

    <script>
        var isLoggedIn = <?php echo isset($_SESSION["user_id"]) ? 'true' : 'false'; ?>;
    </script>
    
    <script src="js/ajax.js"></script> 
    <script src="js/FormValidation.js"></script> 
</body>
</html>
