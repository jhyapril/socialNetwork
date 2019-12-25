<?php
// Declariing variables to prevent errors
$fname = "";
$lname = "";
$email = "";
$email2 = "";
$password = "";
$password2 = "";
$date = ""; //sign up date
$error_array = array(); // Holds error messages
$username = "";
$profile_pic = "";

if (isset($_POST['register_button'])) {
    // Registration form values
    // First name
    $fname = strip_tags($_POST['reg_fname']); //remove html tags
    $fname = str_replace(' ', '', $fname); //remove spaces
    $lname = ucfirst(strtolower($fname)); //uppercase first letter
    $_SESSION['reg_fname'] = $fname; // Stores firt name into session varaible
    // Last name
    $lname = strip_tags($_POST['reg_lname']);
    $lname = str_replace(' ', '', $lname);
    $lname = ucfirst(strtolower($lname));
    $_SESSION['reg_lname'] = $lname;
    // Email
    $email = strip_tags($_POST['reg_email']);
    $email = str_replace(' ', '', $email);
    $email = strtolower($email);
    $_SESSION['reg_email'] = $email;
    // Email 2
    $email2 = strip_tags($_POST['reg_email2']);
    $email2 = str_replace(' ', '', $email2);
    $email2 = strtolower($email2);
    $_SESSION['reg_email2'] = $email2;
    // Password
    $password = strip_tags($_POST['reg_password']);
    $password2 = strip_tags($_POST['reg_password2']);

    $date = date("Y-m-d"); //Gets current date

    if ($email == $email2) {
        // Check if email format is valid
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = filter_var($email, FILTER_VALIDATE_EMAIL);
            // Check if email already exists
            $email_check = mysqli_query($con, "SELECT email FROM users WHERE email='$email'");
            // Count the number of rows returned
            $num_rows = mysqli_num_rows($email_check);
            if ($num_rows > 0) {
                array_push($error_array, "Email already in use<br />");
            }
        } else {
            array_push($error_array, "Invalid email format<br />");
        }
    } else {
        array_push($error_array, "Emails don't match<br />");
    }

    if (strlen($fname) > 25 || strlen($fname) < 2) {
        array_push($error_array, "Your first name must be between 2 and 25 characters<br />");
    }

    if (strlen($lname) > 25 || strlen($lname) < 2) {
        array_push($error_array, "Your last name must be between 2 and 25 characters<br />");
    }

    if ($password != $password2) {
        array_push($error_array, "Your passwords do not match<br />");
    } else {
        if (preg_match('/[^A-Za-z0-9]/', $password)) {
            array_push($error_array, "Your password can only contain english characters or numbers<br />");
        }
    }
    if (strlen($password) > 30 || strlen($password) < 3) {
        array_push($error_array, "Your password must be between 3 and 30 characters<br />");
    }
    if (empty($error_array)) {
        $password = md5($password); // Encrypt password before sending to database

        // Generate username by concatenating first name and last name
        $username = strtolower($fname . "_" . $lname);
        $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
        $i = 0;
        // if username exists add number to username
        while (mysqli_num_rows($check_username_query) != 0) {
            $i++;
            $username = $username . "_" . $i;
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username='$username'");
        }

        // Profile picture assignment
        $rand = rand(1, 2); // random number between 1 and 2
        if ($rand == 1) {
            $profile_pic = "assets/images/profile_pics/defaults/head_deep_blue.png";
        } else if ($rand == 2) {
            $profile_pic = "assets/images/profile_pics/defaults/head_green_sea.png";
        }
        $query = mysqli_query($con, "INSERT INTO users VALUES(NULL, '$fname', '$lname', '$username', '$email', '$password', '$date', '$profile_pic', '0', '0', 'no', ',')");
        array_push($error_array, "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br />");
    }

    // clear session variables
    $_SESSION['reg_fname'] = "";
    $_SESSION['reg_lname'] = "";
    $_SESSION['reg_email'] = "";
    $_SESSION['reg_email2'] = "";
}