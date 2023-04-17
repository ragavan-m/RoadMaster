<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['driver_id'])) {
  header("Location: login.php");
  exit;
}

// Get current driver's ID from session variable
$driver_id = $_SESSION['driver_id'];

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "roadmaster");

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Query driver details from driver table
$sql = "SELECT * FROM driver WHERE id = '$driver_id'";
$result = mysqli_query($conn, $sql);

// Check if query was successful
if (!$result) {
  die("Error: " . mysqli_error($conn));
}

// Get driver details from query result
$driver = mysqli_fetch_assoc($result);

// Get the photo path for the driver
$photo_path = $driver['photopath'];

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>My Profile</title>
  <link rel="stylesheet" href="./bulma.min.css">
  <link rel="shortcut icon" href="res/logo-color.png" type="image/x-icon">

  <style>

    .yellow{
      background-color: #f9cc0b;
    }
   
   .button.is-primary {
    background-color: #f9cc0b;
    }
    .button.is-primary:hover {
        background-color: #e2b90b;
    }

  </style>
</head>
<body>
  <nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
      <a class="navbar-item" href="driverdash.php">
      <img src="./res/logo-no-background.png" alt="Logo">
      </a>

      <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
        <span aria-hidden="true"></span>
      </a>
    </div>

    <div id="navbarBasicExample" class="navbar-menu">
      <div class="navbar-start">
                
      </div>

      <div class="navbar-end">
        <div class="navbar-item">
          <div class="buttons">
            <a class="button yellow" href="logout.php">
              Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </nav>

  <section class="section">
    <div class="container">
      <h1 class="title">My Profile</h1>
      <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
        <tbody>
          <tr>
            <th>Name:</th>
            <td><?php echo $driver['name']; ?></td>
          </tr>
          <tr>
            <th>Email:</th>
            <td><?php echo $driver['email']; ?></td>
          </tr>
          <tr>
            <th>Mobile:</th>
            <td><?php echo $driver['mobile']; ?></td>
          </tr>
          <tr>
            <th>Address:</th>
            <td><?php echo $driver['address']; ?></td>
          </tr>
          <tr>
            <th>Photo:</th>
            <td><img src="<?php echo $photo_path ?>" alt="User Photo" style="max-width: 150px; max-height: 150px;"></td>
          </tr>
        </tbody>
      </table>
      <a class="button is-primary" href="deditprofile.php">Edit Profile</a>
    </div>
    </section>
</body>
</html>