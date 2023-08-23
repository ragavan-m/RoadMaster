<?php
// start the session
session_start();

// check if the user is not logged in, redirect to the login page
if (!isset($_SESSION['driver_id'])) {
  header("Location: login.php");
  exit();
}

// connect to the database
$conn = mysqli_connect("localhost", "root", "", "roadmaster");

// get the user's details from the database
$driver_id = $_SESSION['driver_id'];
$query = "SELECT * FROM driver WHERE id = $driver_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
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


  </style>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="shortcut icon" href="res/logo-color.png" type="image/x-icon">
  <title>Driver Dashboard</title>
  <link rel="stylesheet" href="bulma.min.css">
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
            <a class="navbar-item" href="profile.php">View Profile</a>
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
          <li><a id="book-ride-btn">View Rides<a></li>
          <li><a id="interested-drivers-btn">Committed rides</a></li>
        </ul>
        <!-- <ul class="menu-list">
        </ul> -->
      </aside>
    </div>

    <!-- main content -->
    <div class="column is-10">
      <section class="section">
        <div class="container">
          <h1 class="title">Driver Dashboard</h1>
          <h2 class="subtitle">Welcome, <?php echo $user['name']; ?>!</h2>
          <hr>
          <div id="book-ride-section" style="display:none;">
    <section id="book-ride-form-section" class="section">
    <div class="container">
        <div class="columns is-centered">
            <div class="column is-6">
                <div class="box">
                    <h2 class="title is-4 has-text-centered">View Rides</h2>
                    <!--Code to view rides-->
                </div>
            </div>
        </div>
    </div>
</section>

</div>

<div id="interested-drivers-section" style="display:none;">
  <h2 class="subtitle">Interested Drivers</h2>
  <!--Code to View Committed rides-->
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

</body>
</html>