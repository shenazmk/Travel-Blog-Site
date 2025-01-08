<?php
require_once("db.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); 
    return $data;
}

// Initialize $last_blog_id
$last_blog_id = 0; 

// Get the last_blog_id from the incoming request to only fetch newer posts
if (isset($_GET["last_blog_id"])) {
    $last_blog_id = test_input($_GET["last_blog_id"]);
}

$errors = array();
// Fetch the new blogs (with IDs greater than the provided `last_blog_id`)
try {
    // Connect to the database
    $db = new PDO($attr, $db_user, $db_pwd, $options);

    $num_posts = isset($_GET["num_posts"]) ? (int)$_GET["num_posts"] : 5;  // Default to 5 if not provided

    $query = "SELECT Blogs.blog_id, Blogs.title, Blogs.content, Blogs.timestamp, Users.screenname, Users.avatar, 
                  (SELECT COUNT(*) FROM Comments WHERE Comments.blog_id = Blogs.blog_id) AS comment_count,
                  Blogs.image 
           FROM Blogs
           JOIN Users ON Blogs.user_id = Users.user_id
           WHERE Blogs.blog_id > :last_blog_id
           ORDER BY Blogs.timestamp DESC
           LIMIT :num_posts";  
    
    $result = $db->query($query);
    $posts = ["blogs" => array()];

    while ($row = $result->fetch()) {
        $posts["blogs"][] = $row;
    }

    // If no new posts, add an error message
    if (empty($posts["blogs"])) {
        $errors["Server Error"] = "No new posts found";
    }

} catch (PDOException $e) {
    // Catch any exceptions and add error message
    $errors["Database Error"] = "Error fetching new posts: " . $e->getMessage();
}

// Output the result as JSON
echo json_encode($posts);
?>