<?php
session_start();

// Check if user is already logged in, redirect to success.php if true
if(isset($_SESSION['username'])) {
    header("Location: home.php");
    exit();
}

// Check if the form is submitted
if(isset($_POST['login'])) {
    $conn = new mysqli('localhost', 'root', 'linkIt@7053!', 'bigue');
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // Prepare SQL query
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);
    
    // Check if query returns any row
    if($result->num_rows > 0) {
        // Login successful, set session variables and redirect to success page
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $row['username'];
        header("Location: home.php");
        exit();
    } else {
        // Login failed, redirect to login page with error message
        header("Location: login.php?error=Invalid%20username%20or%20password");
        exit();
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
                @font-face {
            font-family: "MTNBrighterSans";
            src: url(Fonts/MTNBrighterSans-Light.otf);
        }

        body {
            background-color: #dbd5d5; /* Default background color */
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .loginP {
            background: #e2e2e6;
            border: 0;
            border-radius: 8px;
            box-shadow: -100px -100px 300px 0 #fff, 100px 100px 300px 0 #1d0dca17;
            box-sizing: border-box;
            color: #2a1f62;
            cursor: pointer;
            font-family: "MTNBrighterSans", Consolas, Monaco, "Andale Mono", "Ubuntu Mono", monospace;
            font-size: 1rem;
            line-height: 1.5rem;
            transition: .2s;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            height: 29vh;
            width: 50vh;
            word-break: normal;
            word-spacing: normal;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .button {
            font-family: "MTNBrighterSans", Consolas, Monaco, "Andale Mono", "Ubuntu Mono", monospace;
            display: inline-block;
            background-color: #2a1f62;
            color: #fff;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: background-color 0.3s;
            align-items: center;
        }

        .button:hover {
            background-color: #0f024e17;
        }

        .linput {
            font-family: "MTNBrighterSans", Consolas, Monaco, "Andale Mono", "Ubuntu Mono", monospace;
            width: 30vh;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            margin: 5px;
        }

        .linput:focus {
            border-color: #f8f8ff;
            box-shadow: -15px -15px 30px 0 #fff, 15px 15px 30px 0 #1d0dca17;
        }

        label i {
            font-size: 18px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="loginP">
        <!-- Display error message here -->
        <div id="errorMessage" style="display: none; color: red;"></div>
        <form method="post" id="loginForm">

            <input type="text" id="username" name="username" class="linput" required autocomplete="off" placeholder="Username"><br>

            <input type="password" id="password" name="password" class="linput" required autocomplete="off" placeholder="Password"><br>
            <center> <input type="submit" name="login" value="Login" class="button"> </center>
        </form>
    </div>
    <script>
        // Function to show error message and hide it after 5 seconds
        function showError(message) {
            var errorMessage = document.getElementById('errorMessage');
            errorMessage.textContent = message;
            errorMessage.style.display = 'block';
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 5000);
        }

        // Check if there's an error message in the URL parameters
        var urlParams = new URLSearchParams(window.location.search);
        var errorMessage = urlParams.get('error');
        if(errorMessage) {
            showError(errorMessage);
        }

        // Validate form submission
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            var username = document.getElementById('username').value.trim();
            var password = document.getElementById('password').value.trim();

            if(username === '' || password === '') {
                showError('Please enter both username and password.');
                event.preventDefault();
            }
        });
    </script>
</body>
</html>
