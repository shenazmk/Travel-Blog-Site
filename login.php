<?php
require_once("db.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); 
    return $data;
}

// Check whether the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $errors = array();
    $dataOK = TRUE;
    
    // Get and validate the username and password fields
    $email = test_input($_POST["email"]);
    $emailRegex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    if (!preg_match($emailRegex, $email)) {
        $errors["email"] = "Invalid Email";
        $dataOK = FALSE;
    }

    $password = test_input($_POST["password"]);
    $passwordRegex = "/^(?=(.*[^a-zA-Z]))[a-zA-Z0-9^!@#%&*]{6}$/";
    if (!preg_match($passwordRegex, $password)) {
        $errors["password"] = "Invalid Password";
        $dataOK = FALSE;
    }

    // Check whether the fields are not empty
    if ($dataOK) {

        // Connect to the database and verify the connection
        try {
            $db = new PDO($attr, $db_user, $db_pwd, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }

        $query = "SELECT user_id, email, screenname, avatar, dob FROM Users WHERE email ='$email' AND password ='$password'";
        $result = $db->query($query);

        if (!$result) {
            // query has an error
            $errors["Database Error"] = "Could not retrieve user information";
        } elseif ($row = $result->fetch()) {
            // If there's a row, we have a match and login is successful!
            session_start();
            $_SESSION["user_id"] = $row["user_id"];
            $_SESSION["screenname"] = $row["screenname"];
            $_SESSION["email"] = $row["email"];
            $_SESSION["avatar"] = $row["avatar"];
            $_SESSION["dob"] = $row["dob"];
            $db = null;
            header("Location: homepage.php");
            exit();
        } else {
            // login unsuccessful
            $errors["Login Failed"] = "That username/password combination does not exist.";
        }
        $db = null;
    } else {
        $errors['Login Failed'] = "You entered invalid data while logging in.";
        header("Location: login.php");
        exit();
    }
    if(!empty($errors)){
        foreach($errors as $type => $message) {
            echo "$type: $message <br />\n";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <img src="images/logo2.jpg" alt="Logo" class="logo">
    </div>

    <!-- Login Section -->
    <section class="login-section">
        <form class="login-form" id="login-form" action="" method="post">
            <input type="email" id="email" name="email" placeholder="Email">
            <div id="error-text-email" class="error-text-hidden highlight_input">
                Email is invalid
            </div>
            <input type="password" id="password" name="password" placeholder="Password">
            <div id="error-text-password" class="error-text-hidden highlight_input">
                Password must be at least 6 characters long and cannot contain space
            </div>
            <button type="submit">Log In</button>
            <p class= "white-font"> Don't have an account? <a class="white-font" href="signup.php">Sign Up</a></p>
        </form>
    </section>

    <!-- Posts Section (5 Recent Posts) -->
    <section class="posts-section">
        <div class="post">
            <div class="post-header">
                <div class="avatar">
                    <img src="images/pic1.jpg" alt="Avatar">
                </div>
                <div class="screenname">KOHL</div>
                <div class="date-time">27-6-2024 | 10:00 PM</div> <!-- Date and Time aligned right -->
            </div>
            <div class="post-details">
                <div class="post-content">
                    <img class="post-images" src="images/pic2.jpg" alt="Post Image">
                    <h3>Romance in Italy</h3>
                    <p>For lovers of romance and canals, Venice is a must-see. Glide along the famous canals in a
                        gondola, visit St. Mark’s Basilica, and wander through the maze of narrow streets and bridges
                        that give Venice its charm. The Rialto Bridge and
                        Doge’s Palace are other highlights, offering a glimpse into Venice’s storied history.</p>
                        <div class="white-font">
                            <p> <i class="fa-solid fa-message"></i> 12 </p>
                        </div>
    
                    <a href="blogpost.html">See more</a>
                </div>
            </div>
        </div>

        <div class="post">
            <div class="post-header">
                <div class="avatar">
                    <img src="images/pic1.jpg" alt="Avatar">
                </div>
                <div class="screenname">COLE</div>
                <div class="date-time">27-6-2024 | 5:10 PM</div> <!-- Date and Time aligned right -->
            </div>
            <div class="post-details">
                <div class="post-content">
                    <img class="post-images" src="images/pic2.jpg" alt="Post Image">
                    <h3>Summer in North Cyprus</h3>
                    <p>One of the first places to visit is the Kyrenia (Girne) Harbour, a charming old port town with a
                        picturesque harbor lined with colorful boats, cafes, and restaurants. Overlooking the harbor is
                        the majestic Kyrenia Castle,
                        which offers panoramic views of the coast and houses an interesting museum.</p>
                        <div class="white-font"></div>
                            <p> <i class="fa-solid fa-message"></i> 10 </p>
                        <a href="blogpost.html">See more</a>
                    </div>

                    </div>
            </div>
        </div>
        </div>

        <div class="post">
            <div class="post-header">
                <div class="avatar">
                    <img src="images/pic1.jpg" alt="Avatar">
                </div>
                <div class="screenname">DAVID</div>
                <div class="date-time">21-10-2023 | 8:00 PM</div> <!-- Date and Time aligned right -->
            </div>
            <div class="post-details">
                <div class="post-content">
                    <img class="post-images" src="images/pic2.jpg" alt="Post Image">
                    <h3>Trip to the Maldives</h3>
                    <p>During your stay, you can explore the underwater world through snorkeling or scuba diving,
                        encountering vibrant coral reefs, tropical fish, manta rays, and even gentle whale sharks. The
                        serene lagoons are perfect for kayaking, paddleboarding, or simply swimming in the warm, shallow
                        waters.

                        Island hopping is another highlight, allowing you to discover the different atolls, each
                        offering a unique blend of culture and natural beauty. </p>
                        <div class="white-font"></div>
                            <p> <i class="fa-solid fa-message"></i> 9</p>
                        <a href="blogpost.html">See more</a>
                    </div>

                    </div>
            </div>
        </div>
        </div>


        <div class="post">
            <div class="post-header">
                <div class="avatar">
                    <img src="images/pic1.jpg" alt="Avatar">
                </div>
                <div class="screenname">ETHAN</div>
                <div class="date-time">30-10-2023 | 2:00 PM</div> <!-- Date and Time aligned right -->
            </div>
            <div class="post-details">
                <div class="post-content">
                    <img class="post-images" src="images/pic2.jpg" alt="Post Image">
                    <h3>My Stay in Dubai</h3>
                    <p>A trip to Dubai is a perfect mix of glamour, adventure, and rich cultural heritage, offering an
                        unforgettable experience in the heart of the Middle East.
                        In the evenings, Dubai lights up with vibrant nightlife, offering rooftop bars, fine dining, and
                        entertainment shows.
                    </p>
                    <div class="white-font">
                        <p> <i class="fa-solid fa-message"></i> 12 </p>
                    </div>
                    <a href="blogpost.html">See more</a>
                </div>
            </div>
        </div>
        </div>

        <div class="post">
            <div class="post-header">
                <div class="avatar">
                    <img src="images/pic1.jpg" alt="Avatar">
                </div>
                <div class="screenname">JOHN </div>
                <div class="date-time">30-10-2023 | 12:05 PM</div> <!-- Date and Time aligned right -->
            </div>
            <div class="post-details">
                <div class="post-content">
                    <img class="post-images" src="images/pic2.jpg" alt="Post Image">
                    <h3>Hiking in Banff</h3>
                    <p>Banff offers world-class hiking and skiing. In the summer, trails like Johnston Canyon lead you
                        through lush forests to cascading waterfalls, while winter turns Banff into a skiing and
                        snowboarding haven,
                        with resorts like Sunshine Village and Lake Louise Ski Resort providing fantastic slopes.</p>
                        <div class="white-font">
                            <p> <i class="fa-solid fa-message"></i> 10 </p>
                        </div>
    
                        <a href="blogpost.html">See more</a>

                    </div>
            </div>
        </div>
        </div>

    </section>
    <script src="js/FormValidation.js"></script>
</body>

</html>