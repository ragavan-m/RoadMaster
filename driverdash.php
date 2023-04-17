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

// Check for errors
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

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

    table {
  border-collapse: collapse;
  width: 100%;
}

th, td {
  text-align: left;
  padding: 8px;
}

tr:nth-child(even) {
  background-color: #f2f2f2;
}

th {
  background-color: #f9cc0b;
  color: white;
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
      <a class="navbar-item" href="driverdash.php">
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
          <li><a href="dprofile.php">View Profile</a></li>
        </ul>
        <ul class="menu-list">
          <li><a id="view-rides-btn">View Rides</a></li>
        </ul>
        <ul class="menu-list">
          <li><a id="committed-rides-btn">Committed Rides</a></li>
        </ul>
      </aside>

    </div>

    <!-- main content -->
    <div class="column is-10">
      <section class="section">
        <div class="container">
          <h1 class="title">Driver Dashboard</h1>
          <h2 class="subtitle">Welcome, <?php echo $user['name']; ?>!</h2>
          <hr>
          <div id="view-rides-section">
            <table is-bordered is-striped is-narrow is-hoverable is-fullwidth>
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Pickup</th>
                  <th>Destination</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Mobile</th>
                  <th>Email</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // connect to database and fetch rides
                $conn = mysqli_connect("localhost", "root", "", "roadmaster");
                $query = "SELECT * FROM rides";
                $result = mysqli_query($conn, $query);

                $sql = "SELECT r.*, u.name, u.mobile, u.email FROM rides r JOIN user u ON r.user_id = u.id WHERE r.driver_id IS NULL ORDER BY r.id DESC";
                $result = mysqli_query($conn, $sql);

                if (!$result) {
                  // there was an error in the query, print the error message
                  echo "Error: " . mysqli_error($conn);
                } else {
                  // loop through all rides and display them in the table
                  while ($ride = mysqli_fetch_assoc($result)) {
                    // display ride information in table row
                    echo "<tr>";
                    echo "<td>" . $ride['name'] . "</td>";
                    echo "<td>" . $ride['pickup_location'] . "</td>";
                    echo "<td>" . $ride['destination'] . "</td>";
                    echo "<td>" . $ride['pickup_date'] . "</td>";
                    echo "<td>" . $ride['pickup_time'] . "</td>";
                    echo "<td>" . $ride['mobile'] . "</td>";
                    echo "<td>" . $ride['email'] . "</td>";
                    echo "<td><button class='interested-btn' data-ride-id='" . $ride['id'] . "'>Interested</button></td>";
                    echo "</tr>";
                  }
                }

                ?>
              </tbody>
            </table>
          </div>

<div id="committed-rides-section" style="display:none;">
          <div id="committed-rides-section">
            <h2 class="subtitle">Committed Rides</h2>
            <table class="table">
              <thead>
                <tr>
                  <th>Ride ID</th>
                  <th>Passenger</th>
                  <th>Start Location</th>
                  <th>End Location</th>
                  <th>Start Time</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php
                // get the committed rides for this driver
                $query = "SELECT rides.*, user.name, user.mobile, user.email
          FROM rides
          JOIN user ON rides.user_id = user.id
          WHERE rides.driver_id = $driver_id AND rides.status = 'open';";
                $result = mysqli_query($conn, $query);
                if (!$result) {
                  echo "Error: " . mysqli_error($conn);
                } elseif (mysqli_num_rows($result) > 0) {
                  // display the table headers
                  echo "<table>";
                  echo "<tr><th>Name</th><th>Pickup</th><th>Destination</th><th>Date</th><th>Time</th><th>Mobile</th><th>Email</th></tr>";
                  // loop through the results and display each row as a table row
                  while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>";
                    echo "<td>{$row['name']}</td>";
                    echo "<td>{$row['pickup_location']}</td>";
                    echo "<td>{$row['destination']}</td>";
                    echo "<td>{$row['pickup_date']}</td>";
                    echo "<td>{$row['pickup_time']}</td>";
                    echo "<td>{$row['mobile']}</td>";
                    echo "<td>{$row['email']}</td>";
                    echo "</tr>";
                  }
                  echo "</table>";
                } else {
                  echo "No committed rides.";
                }

                ?>

              </tbody>
            </table>
          </div>
        </div>
      </section>
    </div>
    </div>
    <!-- script -->
    <script>
  $(document).ready(function() {
    // hide all sections by default
    $("#view-rides-section").hide();
    $("#committed-rides-section").hide();

    // add click listener for view rides button
    $("#view-rides-btn").click(function() {
      // hide all sections
      $("#view-rides-section").show();
      $("#committed-rides-section").hide();
    });

    // add click listener for committed rides button
    $("#committed-rides-btn").click(function() {
      // hide all sections
      $("#view-rides-section").hide();
      $("#committed-rides-section").show();
    });

    // add click listener for complete ride button
    $(".complete-ride-btn").click(function() {
      // get the ride id from the data attribute
      var rideId = $(this).data("ride-id");

      // send ajax request to complete the ride
      $.ajax({
        url: "complete-ride.php",
        method: "POST",
        data: {
          ride_id: rideId
        },
        success: function(response) {
          // reload the page
          location.reload();
        }
      });
    });
  });
</script>
    
</body>

</html>

<?php
// Close database connection
mysqli_close($conn);
?>