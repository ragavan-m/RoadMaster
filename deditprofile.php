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

// Check if form has been submitted
if (isset($_POST['submit'])) {
  // Get form values
  $name = $_POST['name'];
  $email = $_POST['email'];
  $mobile = $_POST['mobile'];
  $address = $_POST['address'];
  $old_password = $_POST['old_password'];
  $new_password = $_POST['new_password'];

  // Verify old password
  $sql = "SELECT password FROM driver WHERE id = '$driver_id'";
  $result = mysqli_query($conn, $sql);
  if (!$result) {
    die("Error: " . mysqli_error($conn));
  }
  $row = mysqli_fetch_assoc($result);
  $hashed_password = $row['password'];
  if (!password_verify($old_password, $hashed_password)) {
    die("Error: Incorrect old password.");
  }

  // Hash and update new password
  if (!empty($new_password)) {
    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
    $sql = "UPDATE driver SET name = '$name', email = '$email', mobile = '$mobile', address = '$address', password = '$hashed_new_password' WHERE id = '$driver_id'";
  } else {
    $sql = "UPDATE driver SET name = '$name', email = '$email', mobile = '$mobile', address = '$address' WHERE id = '$driver_id'";
  }
  $result = mysqli_query($conn, $sql);

  // Check if query was successful
  if (!$result) {
    die("Error: " . mysqli_error($conn));
  }

  // Update driver details in session variable
  $_SESSION['driver_name'] = $name;
  $_SESSION['driver_email'] = $email;
  $_SESSION['driver_mobile'] = $mobile;
  $_SESSION['driver_address'] = $address;

  // Redirect to profile page
  header("Location: dprofile.php");
  exit;
}

// Close database connection
mysqli_close($conn);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Profile</title>
  <link rel="stylesheet" href="./bulma.min.css">
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
        <a class="button is-primary" href="dprofile.php">
          <strong><?php echo $driver['name']; ?></strong>
        </a>
        <a class="button is-light" href="logout.php">
          Log out
        </a>
      </div>
    </div>
  </div>
</div>
</nav>
  <section class="section">
    <div class="container">
      <h1 class="title">Edit Profile</h1>
      <form method="post">
        <div class="field">
          <label class="label">Name</label>
          <div class="control">
            <input class="input" type="text" name="name" value="<?php echo $driver['name']; ?>" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Email</label>
          <div class="control">
            <input class="input" type="email" name="email" value="<?php echo $driver['email']; ?>" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Mobile</label>
          <div class="control">
            <input class="input" type="tel" name="mobile" value="<?php echo $driver['mobile']; ?>" required>
          </div>
        </div>
        <div class="field">
          <label class="label">Address</label>
          <div class="control">
            <input class="input" type="text" name="address" value="<?php echo $driver['address']; ?>" required>
          </div>
        </div>
        <hr>
        <h2 class="subtitle">Change Password</h2>
        <div class="field">
          <label class="label">Old Password</label>
          <div class="control">
            <input class="input" type="password" name="old_password" required>
          </div>
        </div>
        <div class="field">
          <label class="label">New Password</label>
          <div class="control">
            <input class="input" type="password" name="new_password" required>
          </div>
        </div>
        <div class="field is-grouped">
          <div class="control">
            <button class="button is-primary" type="submit" name="submit">Save Changes</button>
          </div>
          <div class="control">
            <a class="button is-link" href="dprofile.php">Cancel</a>
          </div>
        </div>
      </form>
    </div>
  </section>
</body>
</html>