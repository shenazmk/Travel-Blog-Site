<?php
require_once("db.php");
session_start();

// Check if the `id` parameter is set in the URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $blogPostId = $_GET['id']; // Get the blog post ID from the URL
} else {
    echo "No blog post ID provided.";
    exit();
}

// Fetch the blog post details
$sqlPost = "SELECT Blogs.title, Blogs.content, Blogs.image, Blogs.timestamp, Users.screenname, Users.avatar 
            FROM Blogs
            JOIN Users ON Blogs.user_id = Users.user_id
            WHERE Blogs.blog_id = ?";
$stmtPost = $pdo->prepare($sqlPost);
$stmtPost->execute([$blogPostId]);
$post = $stmtPost->fetch();

if (!$post) {
    echo "Post not found!";
    exit();
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id']) && isset($_POST['comment_content'])) {
    $userId = $_SESSION['user_id'];
    $commentContent = trim($_POST['comment_content']);
    $currentDateTime = date("Y-m-d H:i:s");

    // Validate the comment content
    if (empty($commentContent)) {
        $error = "Comment cannot be blank.";
    } elseif (strlen($commentContent) > 1000) {
        $error = "Comment exceeds 1000 characters.";
    } else {
        // Save the comment to the database
        $sql = "INSERT INTO Comments (blog_id, user_id, content, timestamp) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$blogPostId, $userId, $commentContent, $currentDateTime])) {
            // Redirect to the same page to display the new comment
            header("Location: blogpost.php?id=$blogPostId");
            exit();
        } else {
            $error = "Failed to save comment. Please try again.";
        }
    }
}

// Handle voting on comments
if (isset($_POST['vote_type']) && isset($_POST['comment_id']) && isset($_SESSION['user_id'])) {
    $voteType = $_POST['vote_type']; // 1 for upvote, 0 for downvote
    $commentId = $_POST['comment_id'];
    $userId = $_SESSION['user_id'];

    if (!in_array($voteType, [1, 0])) {
        die("Invalid vote type.");
    }

    // Check if the user already voted on the comment
    $query = "SELECT vote_type FROM Votes WHERE user_id = ? AND comment_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$userId, $commentId]);
    $existingVote = $stmt->fetch();

    if ($existingVote) {
        // Update the existing vote if it's different
        if ($existingVote['vote_type'] != $voteType) {
            $updateVote = "UPDATE Votes SET vote_type = ? WHERE user_id = ? AND comment_id = ?";
            $stmt = $pdo->prepare($updateVote);
            $stmt->execute([$voteType, $userId, $commentId]);
        }
    } else {
        // Insert a new vote
        $insertVote = "INSERT INTO Votes (user_id, comment_id, vote_type) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($insertVote);
        $stmt->execute([$userId, $commentId, $voteType]);
    }

    // Redirect to the same blog post
    header("Location: blogpost.php?id=$blogPostId");
    exit();
}

// Fetch comments for the blog post and order by voting score
$sqlComments = "SELECT Comments.comment_id, Comments.content, Comments.timestamp, Users.screenname, Users.avatar, 
                       (SELECT SUM(vote_type = 1) FROM Votes WHERE Votes.comment_id = Comments.comment_id) AS up_votes,
                       (SELECT SUM(vote_type = -1) FROM Votes WHERE Votes.comment_id = Comments.comment_id) AS down_votes
                FROM Comments
                JOIN Users ON Comments.user_id = Users.user_id
                WHERE Comments.blog_id = ?
                ORDER BY (up_votes - down_votes) DESC, Comments.timestamp ASC";
$stmtComments = $pdo->prepare($sqlComments);
$stmtComments->execute([$blogPostId]);
$comments = $stmtComments->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/dropdown.js"></script> <!-- Ensure dropdown JS is loaded -->
</head>
<body>
<div class="grid">
    <div class="header">
        <img src="images/logo2.jpg" alt="Logo" class="logo">
        <div class="user-area">
            <?php if (isset($_SESSION['user_id'])): ?>
                <img src="images/<?php echo htmlspecialchars($_SESSION['user_avatar']); ?>" alt="User Avatar">
                <span class="screenname"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                <div class="dropdown">
                    <button class="menu">
                        <img src="images/menu.svg" alt="dropdown menu" />
                    </button>
                    <div class="dropdown-content">
                        <a href="homepage.php">Home Page</a>
                        <a href="blogmanagement.php">Blog Management Page</a>
                        <a href="blogcreation.php">Create a Post</a>
                        <a href="login.php">Logout</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="content">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        <img src="images/<?php echo htmlspecialchars($post['image']); ?>" alt="Blog Post Image" class="post-image-1">
        <div class="meta">
            <p>By <?php echo htmlspecialchars($post['screenname']); ?> || <?php echo date("d-m-Y | h:i A", strtotime($post['timestamp'])); ?></p>
        </div>
        <div class="text">
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
        </div>

        <div class="comments">
            <h2>Comments</h2>
            <?php if (isset($error)): ?>
                <p class="error"><?php echo $error; ?></p>
            <?php endif; ?>

            <?php foreach ($comments as $comment): ?>
                <div class="comment">
                    <img src="images/<?php echo htmlspecialchars($comment['avatar']); ?>" alt="<?php echo htmlspecialchars($comment['screenname']); ?> Avatar">
                    <div>
                        <div class="date-time-comment">
                            <h4><?php echo htmlspecialchars($comment['screenname']); ?></h4>
                            • <?php echo date("d-m-Y | h:i A", strtotime($comment['timestamp'])); ?>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
                        <div class="icons">
                            <div class="votes">
                                <img src="images/arrow-up.svg" alt="up-vote" />
                                <p><?php echo $comment['up_votes'] ?? 0; ?></p>
                            </div>
                            <div class="votes">
                                <img src="images/arrow-down.svg" alt="down-vote" />
                                <p><?php echo $comment['down_votes'] ?? 0; ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>

            <form id="comment-form" method="POST">
                <div class="form-input-grid2">
                    <input type="text" id="comment-content" name="comment_content" placeholder="Start commenting on this post..." required />  
                    <div class="align-right">
                        <input class="submit" type="submit" value="Submit"/>
                        <div id="numOfChar">Character count: 0</div>
                        <div id="remaining_chars">Characters remaining: 1000</div>  
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<footer class="footer-space">
        <p class="footer-text">CS 215 Assignment 5 Solution: Group 5</p>
    </footer>
<script src="js/FormValidation.js"></script>
</body>
</html>
