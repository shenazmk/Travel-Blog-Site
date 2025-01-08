<?php
require_once("db.php");

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data); 
    return $data;
}

$errors = array();
$email = "";
$screenname = "";
$password = "";
$dob = "";

// Check whether the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // If we got here through a POST submitted form, process the form

    // Collect and validate form inputs
    $email = test_input($_POST["email"]);
    $screenname = test_input($_POST["sname"]);
    $password = test_input($_POST["pword"]);
    $dob = test_input($_POST["dob"]);

    // Form Field Regular Expressions
    $snameRegex = "/^\w+$/";
    $emailRegex = "/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/";
    $passwordRegex = "/^(?=(.*[^a-zA-Z]))[a-zA-Z0-9^!@#%&*]{6}$/";
    $dobRegex = "/^\d{4}[-]\d{2}[-]\d{2}$/";

    // Validate the form inputs against their Regexes
    if (!preg_match($emailRegex, $email)) {
        $errors["email"] = "Invalid Email";
    }
    if (!preg_match($snameRegex, $screenname)) {
        $errors["screenname"] = "Invalid ScreenName";
    }
    if (!preg_match($passwordRegex, $password)) {
        $errors["password"] = "Invalid Password";
    }
    if (!preg_match($dobRegex, $dob)) {
        $errors["dob"] = "Invalid DOB";
    }

    $target_file = "";

    try {
        $db = new PDO($attr, $db_user, $db_pwd, $options);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int)$e->getCode());
    }

    $query = "SELECT screenname FROM Users WHERE screenname='$screenname'";

    $result = $db->query($query);
    $match = $result->fetch();

    // If username is already taken
    if ($match) {
        $errors["username-taken"] = "A user with that username already exists.";
    }
    
    //If there are no errors so far we can try inserting a user
    if (empty($errors)) {
        $query = "INSERT INTO Users (screenname, email, password, dob, avatar) VALUES ('$screenname', '$email', '$password', '$dob', 'avatar_temp')";
        $result = $db->exec($query);

        if (!$result) {
            $errors["Database Error:"] = "Failed to insert user";
        } else {
            // Directory where the avatars will be uploaded.
            $target_dir = "uploads/";
            $uploadOk = TRUE;
        
            // Fetch the image filetype
            $imageFileType = strtolower(pathinfo($_FILES["pp"]["name"],PATHINFO_EXTENSION));

            $uid = $db->lastInsertId();
            
            // Make the name of the file to be uploaded including the user ID
            $target_file = $target_dir . $uid . "." . $imageFileType;

            // Check whether the file exists in the uploads directory
            if (file_exists($target_file)) {
                $errors["profilephoto-taken"] = "Sorry, file already exists. ";
                $uploadOk = FALSE;
            }
                
            // Check whether the file is not too large
            if ($_FILES["pp"]["size"] > 1000000) {
                $errors["profilephoto-size"] = "File is too large. Maximum 1MB. ";
                $uploadOk = FALSE;
            }

            // Check image file type
            if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
                $errors["profilephoto-type"] = "Bad image type. Only JPG, JPEG, PNG & GIF files are allowed. ";
                $uploadOk = FALSE;
            }
                            
            // Check if $uploadOk still TRUE after validations
            if ($uploadOk) {
                // Move the user's avatar to the uploads directory and capture the result as $fileStatus.
                $fileStatus = move_uploaded_file($_FILES["pp"]["tmp_name"], $target_file);

                // Check $fileStatus:
                if (!$fileStatus) {
                    $errors["profilephoto-upload"] = "Sorry, file could not be uploaded.";
                    $uploadOK = FALSE;
                }
            }
            
            // Check if $uploadOk still TRUE after attempt to move
            if (!$uploadOk)
            {
                $query = "DELETE FROM Users WHERE user_id ='$uid'";
                $result = 0;
                $result = $db->exec($query);

                if (!$result) {
                    $errors["Database Error"] = "Could not delete user when avatar upload failed";
                }
                $db = null;
            } else {
                $query = "UPDATE Users SET avatar = '$target_file' WHERE user_id = '$uid'";
                $result = $db->exec($query);
                if (!$result) {
                    $errors["Database Error:"] = "Could not update avatar";
                } else {
                    $db = null;
                    header("Location: login.php");
                    exit();
                }
            } // Image was uploaded
        } // Insert user query worked
    }

    if (!empty($errors)) {
        foreach($errors as $type => $message) {
            print("$type: $message \n<br />");
        }
    }
} // submit method was POST
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="css/style.css" />
    <script src="js/EventHandlerSignUp.js" ></script>
</head>

<body>
    <div class="grid">
        <div class="header">
        <img src="images/logo2.jpg" alt="Logo" class="logo">
        </div>
        <main id="main-center">
            <form class="auth-form" method="post" action="" id="mySignupForm" enctype="multipart/form-data">
                <div class="form-input-grid">
                    <label for="email">Email Address</label> 
                    <input type="text" id="email" name="email" placeholder="e.g. johnMann14@gmail.com"/>   
                    &nbsp;
                     <div id="err-email" class="error hidden">
                        <p id="err-email-msg" class="none">Email address invalid!</p>
                     </div> 

                    <label for="sname">Screen name</label>
                    <input type="text" id="sname" name="sname" placeholder="e.g. JohnMann"/> 
                    &nbsp; 
                    <div id="err-sname" class="error hidden">
                        <p id="err-sname-msg" class="none">Screen name invalid! No spaces and/or other non-word characters allowed.</p>
                    </div>

                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob"/> 
                    &nbsp; 
                    <div id="err-dob" class="error hidden">
                        <p id="err-dob-msg" class="none">Date of birth invalid!</p>
                    </div> 

                    <label for="pp">Avatar</label>
                    <input type="file" id="pp" name="pp" value="Choose file"/> 
                    &nbsp; 
                    <div id="err-pp" class="error hidden">
                        <p id="err-pp-msg" class="none">Avatar invalid! No file uploaded.</p>
                    </div> 

                    <label for="pword">Password</label>
                    <input type="password" id="pword" name="pword"/> 
                    &nbsp; 
                    <div id="err-pword" class="error hidden">
                        <p id="err-pword-msg" class="none">Password invalid! Must be 6 characters long and contain at least one non-letter character.</p>
                    </div> 

                    <label for="cpword">Confirm Password</label>
                    <input type="password" id="cpword" name="cpword"/> 
                    &nbsp; 
                    <div id="err-cpword" class="error hidden">
                        <p id="err-cpword-msg" class="none">Passwords do not match!</p>
                    </div>

                </div>

                <div class="align-right">
                <input class="submit" type="submit" value="Signup" href="homepage.html"/>
                </div>
            </form>
            <div class="form-note">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </main>
        <footer class="footer-space">
            <p class="footer-text">CS 215 Assignment 3 Solution: Group 5</p>
        </footer>
    </div>
    <script src="js/EventRegistrationSignUp.js" ></script>
</body>


</html>