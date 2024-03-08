<?php
session_start();

// Check if user is not logged in, redirect to login.php if true
if(!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Logout functionality
if(isset($_POST['logout'])) {
    // Unset username session variable
    unset($_SESSION['username']);
    // Destroy the session
    session_destroy();
    header("Location: login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Security & Safety Section</title> 
    <link rel="stylesheet" href="Design.css">
</head>
<body>

<nav>
    <a href="Home.html">
        <span class="icon">
            <ion-icon name="boat-outline"></ion-icon>
        </span>
        <span class="title">Home</span>
    </a>
    

    <a href="visitor_management.php">
        <span class="icon">
            <ion-icon name="people"></ion-icon>
        </span>
        <span class="title">Visitor</span>
    </a>
          



    <a href="view_logs.php" id="reportLink" onclick="showContent('report')">
        <span class="icon">
            <ion-icon name="reader-outline"></ion-icon>
        </span>
        <span class="title">Report</span>
    </a>   

    <a href="logout.php" class="logout-btn" name="logout">
        <span class="icon">
            <ion-icon name="log-out-outline"></ion-icon>
        </span>
        <span class="title">Logout</span>
    </a> 
</nav>

<!-- Home -->
<div class="cont" id="homeContent">
    <section id="index">
        <h2>Welcome to MTNR Security & Safety Reception Portal</h2>
        <p>We are happy to serve you</p>
    </section>
</div>

<script>
var inactivityTime = 300000; // 5 minutes in milliseconds
var logoutUrl = 'logout.php'; // Replace 'logout.php' with the URL of your logout page

var timeout;

function logout() {
    window.location.href = logoutUrl;
}

function resetTimer() {
    clearTimeout(timeout);
    timeout = setTimeout(logout, inactivityTime);
}

// Add event listeners for user activity
document.addEventListener('mousemove', resetTimer);
document.addEventListener('keypress', resetTimer);
document.addEventListener('scroll', resetTimer);

// Start the timer initially
resetTimer();
</script>

<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="logout.js"></script>
</body>
</html>
