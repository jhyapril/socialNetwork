<?php
require 'config/config.php';

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: register.php");
}
?>

<html>

<head>
    <title>Social Network</title>
    <!-- Javascript -->
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"
        integrity="sha256-BbhdlvQf/xTY9gja0Dq3HiwQF8LaCRTXxZKRutelT44=" crossorigin="anonymous"></script>
    <script src="assets/js/bootstrap.js"></script>

    <!-- CSS -->
    <link rel="stylesheet" type="text/css"
        href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
</head>

<body>
    <div class="top_bar">
        <div class="logo">
            <a href="index.php">SocialNetwork</a>
        </div>
        <nav>
            <a href="<?php echo $userLoggedIn ?>">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="index.php"><i class="fa fa-home fa-lg"></i></a>
            <a href="#"><i class="fa fa-envelope fa-lg"></i></a>
            <a href="#"><i class="fa fa-bell-o fa-lg"></i></a>
            <a href="#"><i class="fa fa-users fa-lg"></i></a>
            <a href="#"><i class="fa fa-cog fa-lg"></i></a>
        </nav>
    </div>

    <div class="wrapper">