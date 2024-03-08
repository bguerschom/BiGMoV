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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs</title>
    <link rel="stylesheet" href="Design.css">
    <style>
        .Scont {
            max-width: 90vw;
            margin: 20px auto;
            font-size: 15px;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }

        .view-options {
            margin-top: 20px;
            text-align: right;
        }

        /* Style for select dropdown */
        #limit {
            padding: 5px;
            font-size: 15px;
        }
    </style>
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

<a href="logout.php" class="logout-btn" name="logout" value="Logout">
    <span class="icon">
        <ion-icon name="log-out-outline"></ion-icon>
    </span>
    <span class="title">Logout</span>
</a> 

</nav>

<div class="Scont">
    <h2>Visitor Logs</h2>

    <!-- Search Form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" id="searchForm">
        <input type="text" name="search" placeholder="Search..." id="searchInput">
    </form>

    <!-- PHP Code for Fetching and Displaying Data -->
    <?php
    $conn = new mysqli("localhost", "root", "linkIt@7053!", "bigue");

    

    // Set default limit to 10 if not provided in URL
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
    $search_query = isset($_GET['search']) ? $_GET['search'] : '';

    // Construct SQL query based on search query
    $sql = "SELECT CONCAT(first_name, ' ', last_name) AS name, id_passport, gender, phone_number, department, visitor_card, reason_for_visit, entry_timestamp, exit_timestamp FROM visitor_logs";
    if (!empty($search_query)) {
        $sql .= " WHERE first_name LIKE '%$search_query%' OR last_name LIKE '%$search_query%' OR id_passport LIKE '%$search_query%' OR department LIKE '%$search_query%' OR reason_for_visit LIKE '%$search_query%'";
    }
    $sql .= " ORDER BY entry_timestamp DESC LIMIT $limit";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo "<table>";
        echo "<tr><th>Name</th><th>ID/Passport</th><th>Gender</th><th>Phone Number</th><th>Department</th><th>Visitor Card</th><th>Reason for Visit</th><th>Entry Time</th><th>Exit Time</th></tr>";
        while($row = $result->fetch_assoc()) {
            echo "<tr><td>".$row["name"]."</td><td>".$row["id_passport"]."</td><td>".$row["gender"]."</td><td>".$row["phone_number"]."</td><td>".$row["department"]."</td><td>".$row["visitor_card"]."</td><td>".$row["reason_for_visit"]."</td><td>".$row["entry_timestamp"]."</td><td>".$row["exit_timestamp"]."</td></tr>";
        }
        echo "</table>";
    } else {
        echo "0 results";
    }
    $conn->close();
    ?>

    <!-- Export Buttons -->
    <div class="view-options">
        <form action="export_to_excel.php" method="post">
            <input type="hidden" name="search_query" value="<?php echo $search_query; ?>">
            <button type="submit">Export to Excel</button>
        </form>
<!--
        <form action="export_to_pdf.php" method="post">
            <input type="hidden" name="search_query" value="<?php echo $search_query; ?>">
            <button type="submit">Export to PDF</button>
        </form>
    </div> -->

    <!-- Form for selecting number of rows to display -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get" class="view-options" id="viewForm">
        <label for="limit">Show rows:</label>
        <select name="limit" id="limit">
            <option value="10" <?php if(isset($_GET['limit']) && $_GET['limit'] == '10') echo 'selected'; ?>>10</option>
            <option value="20" <?php if(isset($_GET['limit']) && $_GET['limit'] == '20') echo 'selected'; ?>>20</option>
            <option value="50" <?php if(isset($_GET['limit']) && $_GET['limit'] == '50') echo 'selected'; ?>>50</option>
            <option value="100" <?php if(isset($_GET['limit']) && $_GET['limit'] == '100') echo 'selected'; ?>>100</option>
        </select>
    </form>
</div>

<!-- Ionicons CDN -->
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

<!-- JavaScript to submit form when typing in the search input field -->
<script>
    let timeoutId;

    document.getElementById('searchInput').addEventListener('input', function() {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(function() {
            document.getElementById('searchForm').submit();
        }, 500); // Adjust the delay as needed (in milliseconds)
    });
</script>


<!-- JavaScript to submit form when select dropdown value changes -->
<script>
    document.getElementById('limit').addEventListener('change', function() {
        document.getElementById('viewForm').submit();
    });
</script>

<!-- Add this JavaScript for automatic logout -->
<script>
    const inactivityTime = 5 * 60 * 1000; // 5 minutes in milliseconds
    let logoutTimer;

    function resetTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(logout, inactivityTime);
    }

    function logout() {
        // Unset all session variables
        session_unset();
        // Destroy the session
        session_destroy();
        // Redirect to login page
        window.location.href = "login.php";
    }

    // Reset the timer on any user interaction
    document.addEventListener('mousemove', resetTimer);
    document.addEventListener('mousedown', resetTimer);
    document.addEventListener('keypress', resetTimer);
    document.addEventListener('scroll', resetTimer);
</script>
<script src="logout.js"></script>
</body>
</html>
