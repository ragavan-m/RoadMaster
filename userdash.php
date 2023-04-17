<?php
// start the session
session_start();

// check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit();
}

// connect to the database
$conn = mysqli_connect("localhost", "root", "", "roadmaster");

// get the user's details from the database
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM user WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!-- Php to handle book a ride form -->
<?php
// Connect to the database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "roadmaster";
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check for errors
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


// Check if form has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Get session data
  $user_id = $_SESSION["user_id"];
  $name = $_SESSION["user_name"];
  $mobile = $_SESSION["user_phone"];
  $email = $_SESSION["user_email"];
  $address = $_SESSION["user_address"];

  // Get form data
  $pickup = isset($_POST["pickup"]) ? $_POST["pickup"] : "";
  $destination = isset($_POST["destination"]) ? $_POST["destination"] : "";
  $date_time = isset($_POST["date_time"]) ? $_POST["date_time"] : "";
  $requirements = isset($_POST["requirements"]) ? $_POST["requirements"] : "";

  // Parse the date and time fields
  $datetime = date_create_from_format('Y-m-d\TH:i', $date_time);
  if ($datetime === false) {
      $errorMessage = "Error: Invalid date and time format";
  } else {
      $date = $datetime->format('Y-m-d');
      $time = $datetime->format('H:i:s');

      // Insert data into rides table
      $sql = "INSERT INTO rides (user_id, name, mobile, email, pickup_location, destination, pickup_date, pickup_time, requirements) VALUES ('$user_id', '$name', '$mobile', '$email', '$pickup', '$destination', '$date', '$time','$requirements')";

      if (mysqli_query($conn, $sql)) {
          $message = "Ride booked successfully!";
      } else {
          $errorMessage = "Error: " . $sql . "<br>" . mysqli_error($conn);
      }
  }
}
// Check if the button has been clicked
if (isset($_POST['cancel_rides'])) {
       
  // Update the rides status to 'closed'
  $query = "UPDATE rides SET status = 'closed' WHERE user_id = $user_id";
  $result = mysqli_query($conn, $query);
  
  // Display a success message
  if ($result) {
      $successMessage = 'All rides have been closed.';
  } else {
      $errorMessage = 'There was an error closing the rides.';
  }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <style>
    .navbar-menu {
      align-items: center;
    }
    .navbar-item.has-dropdown {
      margin-left: auto;
    }
    .columns {
      height: 100vh;
    }
    .column.is-2 {
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }
    .menu-label {
      margin-bottom: 0.5rem;
    }
    .menu-list {
      margin-top: 0;
    }

    .notification-col {
    /* background-color: #f9cc0b; */
    color: #000;
  }

  </style>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link rel="stylesheet" href="bulma.min.css">
  <link rel="shortcut icon" href="res/logo-color.png" type="image/x-icon">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

  <!-- navbar -->
  <nav class="navbar is-light">
    <div class="navbar-brand">
      <a class="navbar-item" href="userdash.php">
        <img src="./res/logo-no-background.png" alt="Logo">
      </a>
      <div class="navbar-burger" data-target="navbarMenu">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>

    <div id="navbarMenu" class="navbar-menu">
      <div class="navbar-end">
        <div class="navbar-item has-dropdown is-hoverable">
          <a class="navbar-link">
            <?php echo $user['name']; ?>
          </a>
          <div class="navbar-dropdown">
            <a class="navbar-item" href="logout.php">Logout</a>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <!-- main content -->
  <div class="columns">
    <!-- aside bar -->
    <div class="column is-2">
      <aside class="menu">
        <ul class="menu-list">
          <li><a href="uprofile.php">View Profile</a></li>
        </ul>
        <ul class="menu-list">
          <li><a id="book-ride-btn">Book a Ride</a></li>
        </ul>
        <ul class="menu-list">
          <li><a id="interested-drivers-btn">List of Interested Drivers</a></li>
        </ul>
        
      </aside>
    </div>

    <!-- main content -->
    <div class="column is-10">
      <section class="section">
        <div class="container">
          <h1 class="title">User Dashboard</h1>
          <h2 class="subtitle">Welcome, <?php echo $user['name']; ?>!</h2>
          <hr>
          <div class="notification-col">
  <?php if (isset($errorMessage)): ?>
    <div class="notification error">
      <?php echo $errorMessage; ?>
    </div>
  <?php endif; ?>

  <?php if (isset($successMessage)): ?>
    <div class="notification success">
      <?php echo $successMessage; ?>
    </div>
  <?php endif; ?>
</div>

            

<!-- Book a ride section -->
<?php
// Check if user has any open rides
$openRideSql = "SELECT * FROM rides WHERE user_id = '$user_id' AND status = 'open'";
$openRideResult = mysqli_query($conn, $openRideSql);
if (mysqli_num_rows($openRideResult) > 0) {
    $errorMessage = "You already have an open ride.";
} else {
    $errorMessage = "";
}
?>

<div id="book-ride-section">
    <section id="book-ride-form-section" class="section">
        <div class="container">
            <?php if ($errorMessage): ?>
                <div class="notification is-danger"><?php echo $errorMessage; ?></div>
                <div class="text-center mt-3">
                <div class="is-flex justify-content-center">
                <form method="POST">
                  <button class="button is-warning" name="cancel_rides" type="submit">Cancel All Rides</button>
                </form>
                </div>

                </div>
            <?php else: ?>
                <div class="columns is-centered">
                    <div class="column is-6">
                        <div class="box">
                            <h2 class="title is-4 has-text-centered">Book a Ride</h2>
                            <form action="" method="post">
                                <div class="field">
                                    <label class="label">Pickup Location</label>
                                    <div class="control">
                                        <input class="input" type="text" name="pickup" placeholder="Enter pickup location">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Destination</label>
                                    <div class="control">
                                        <input class="input" type="text" name="destination" placeholder="Enter destination">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Date and Time</label>
                                    <div class="control">
                                        <input class="input" type="datetime-local" name="date_time">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Number of Passengers</label>
                                    <div class="control">
                                        <input class="input" type="number" name="passengers" min="1" max="10">
                                    </div>
                                </div>
                                <div class="field">
                                    <label class="label">Special Requirements</label>
                                    <div class="control">
                                        <textarea class="textarea" name="requirements" placeholder="Enter any special requirements"></textarea>
                                    </div>
                                </div>
                                <div class="field">
                                    <div class="control has-text-centered">
                                        <button class="button is-primary book-ride-btn" type="submit">Book Ride</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>


<div id="interested-drivers-section" style="display:none;">
  <h2 class="subtitle">Interested Drivers</h2>
  <section class="section">
  <?php
// connect to the database
$conn = mysqli_connect("localhost", "root", "", "roadmaster");

// Retrieve the current ride ID for the user
$ride_id = ''; // Initialize the variable
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT id FROM rides WHERE user_id = $user_id AND status = 'open'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $ride_id = $row['id'];
    }
}

// Query the interested drivers for the current ride
if (!empty($ride_id)) {
    $query = "SELECT d.name, d.phone, d.email
              FROM interested_drivers id
              JOIN drivers d ON id.driver_id = d.id
              WHERE id.ride_id = $ride_id";
    $result = mysqli_query($conn, $query);
}

// Create the table of interested drivers
if (!empty($result) && mysqli_num_rows($result) > 0) {
    echo "<table>
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>".$row['name']."</td>
                <td>".$row['phone']."</td>
                <td>".$row['email']."</td>
            </tr>";
    }
    echo "</tbody></table>";
} else {
    echo "<p>No interested drivers found for this ride.</p>";
}
?> 
  </section>
</div>

  <!-- scripts -->
  <script>
   // get the book ride and interested drivers buttons
var bookRideBtn = document.getElementById("book-ride-btn");
var interestedDriversBtn = document.getElementById("interested-drivers-btn");

// get the book ride and interested drivers sections
var bookRideSection = document.getElementById("book-ride-section");
var interestedDriversSection = document.getElementById("interested-drivers-section");

// add event listeners to the buttons
bookRideBtn.addEventListener("click", function() {
// hide the interested drivers section
interestedDriversSection.style.display = "none";

// show the book ride section
bookRideSection.style.display = "block";
});

interestedDriversBtn.addEventListener("click", function() {
// hide the book ride section
bookRideSection.style.display = "none";

// show the interested drivers section
interestedDriversSection.style.display = "block";
});
</script>
<!-- JavaScript code to reveal hamburger menu -->
<script>
  // Select the hamburger icon and the navbar menu
  const burger = document.querySelector('.navbar-burger');
  const navbarMenu = document.querySelector('.navbar-menu');

  // Add a click event listener to the hamburger icon
  burger.addEventListener('click', () => {
    // Toggle the 'is-active' class on both the hamburger icon and the navbar menu
    burger.classList.toggle('is-active');
    navbarMenu.classList.toggle('is-active');
  });
</script>
</body>
</html>


<?php
// Close database connection
mysqli_close($conn);
?>