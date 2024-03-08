<?php
session_start(); // Start the session

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


$inserted = false; // Flag to track if data is successfully inserted
$exit_recorded = false; // Flag to track if exit record is successfully recorded
$search_result = false; // Flag to track if search result found

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user is logged in
    if(isset($_SESSION['username'])) {
        $conn = new mysqli("localhost", "root", "linkIt@7053!", "bigue");

        // Recording Entry
        if(isset($_POST['action']) && $_POST['action'] == 'entry') {
            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $id_passport = $_POST['id_passport'];
            $gender = $_POST['gender'];
            $phone_number = $_POST['phone_number'];
            $department = $_POST['department'];
            $visitor_card = $_POST['card'];
            $reason_for_visit = $_POST['reason_for_visit'];
            $entry_username = $_SESSION['username']; // Retrieve user ID from session

            // Insert visitor details into the database
            $sql_insert_visitor = "INSERT INTO visitor_logs (first_name, last_name, id_passport, gender, phone_number, department, visitor_card, reason_for_visit, entry_username) 
                                    VALUES ('$first_name', '$last_name', '$id_passport', '$gender', '$phone_number', '$department', '$visitor_card', '$reason_for_visit', '$entry_username')";
            
            if ($conn->query($sql_insert_visitor) === TRUE) {
                $inserted = true;
            } else {
                echo "Error: " . $sql_insert_visitor . "<br>" . $conn->error;
            }
        }

        // Recording Exit
        elseif(isset($_POST['action']) && $_POST['action'] == 'exit') {
            $id_passport = $_POST['id_passport'];
            $exit_username = $_SESSION['username']; // Retrieve user ID from session

            // Mark the entry as exit
            $sql_exit = "UPDATE visitor_logs SET exit_timestamp = CURRENT_TIMESTAMP, exit_username = '$exit_username' WHERE id_passport = '$id_passport' AND exit_timestamp IS NULL";

            if ($conn->query($sql_exit) === TRUE) {
                $exit_recorded = true;
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }

        // Searching Visitor
        elseif(isset($_POST['action']) && $_POST['action'] == 'search') {
            $id_passport = $_POST['id_passport'];

            // Search for the visitor entry
            $sql_search = "SELECT * FROM visitor_logs WHERE id_passport = '$id_passport' AND exit_timestamp IS NULL";
            $result_search = $conn->query($sql_search);

            if ($result_search->num_rows > 0) {
                $search_result = $result_search->fetch_assoc();
            }
        }

        $conn->close();
    } else {
        echo "User not logged in"; // Optionally, handle case where user is not logged in
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Management</title>
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

<div class="container">
    <div class="left-column">
        <!-- Form for recording visitor entry -->
        <h2 class="Left">Record a visitor</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="action" value="entry">
            <input type="text" name="first_name" required placeholder="First Name"><br>
            <input type="text" name="last_name" required placeholder="Last Name"><br>
            <input type="text" name="id_passport" required placeholder="ID or Passport Number"maxlength="16"><br>

            <select name="gender" required>
                <option disabled selected>Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select><br>
            <input type="text" name="phone_number" required placeholder="Phone Number 25078 / 25079" maxlength="12"><br>

            <select id="visitorDepartment" name="department" required onchange="updateVisitorCards()">
                <option disabled selected>Select Department & Section</option>
                <?php
                $departments = array(
                    "CEO's Office",
                    "Consumer",
                    "Corporate Services",
                    "Customer Operation Section",
                    "EBU",
                    "Finance",
                    "Human Resources",
                    "Internal Audit & Forencics",
                    "IT",
                    "Network",
                    "Procurement Section",
                    "Risk & Compliance",
                    "Security & safety Section",
                    "Sales and Distribution"
                );

                foreach ($departments as $dept) {
                    echo "<option value='$dept'>$dept</option>";
                }
                ?>
            </select><br>

            <select id="visitorCard" name="card" required>
                <!-- Visitor card options will be added here dynamically based on department -->
            </select><br>
            <textarea name="reason_for_visit" required placeholder="Reason for Visit: "></textarea>
            <div id="notificationLeft" style="display: none;">
            <p>Visitor record inserted successfully!</p>
            </div>
            <button type="submit">Register a visitor</button>

            
        </form>
    </div>
    <div class="right-column">
        <!-- Form for searching and exiting visitor -->
        <h2 class="Right">Search and exit visitor</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="action" value="search">
            <input type="text" name="id_passport" required placeholder="Visitor ID/Passport"><br>
            <button type="submit">Search</button>
        </form>



        <div id="notificationRight" style="display: none;">
        <p>Exit record recorded successfully!</p>
    </div>

        <?php if($search_result): ?>
        <h3>Visitor Details</h3>
        <p>First Name: <?php echo $search_result['first_name']; ?></p>
        <p>Last Name: <?php echo $search_result['last_name']; ?></p>
        <p>Gender: <?php echo $search_result['gender']; ?></p>
        <p>Phone Number: <?php echo $search_result['phone_number']; ?></p>
        <p>Visitor Card: <?php echo $search_result['visitor_card']; ?></p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <input type="hidden" name="action" value="exit">
            <input type="hidden" name="id_passport" value="<?php echo $search_result['id_passport']; ?>">
            <button type="submit">Exit</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
<?php if($inserted): ?>
// Show notification for visitor registration
setTimeout(function() {
    document.getElementById('notificationLeft').style.display = "block";
    setTimeout(function() {
        document.getElementById('notificationLeft').style.display = "none";
    }, 5000); // 5 seconds
}, 100);
<?php endif; ?>

<?php if($exit_recorded): ?>
// Show notification for visitor exit
setTimeout(function() {
    document.getElementById('notificationRight').style.display = "block";
    setTimeout(function() {
        document.getElementById('notificationRight').style.display = "none";
    }, 5000); // 5 seconds
}, 100);
<?php endif; ?>

function updateVisitorCards() {
    let visitorDepartment = document.getElementById('visitorDepartment').value;
    let visitorCard = document.getElementById('visitorCard');
    visitorCard.innerHTML = ''; // Clear previous options

    let cardsPerDepartment = 20; // Number of cards per department

    // Add cards for the selected department or section
    for (let i = 1; i <= cardsPerDepartment; i++) {
        let option = document.createElement('option');
        let cardValue = `${visitorDepartment} Visitor ${i}`;
        option.value = cardValue;
        option.textContent = cardValue;
        visitorCard.appendChild(option);
    }
}
</script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<script src="logout.js"></script>
</body>
</html>
