<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

// Get current user's ID from session variable
$user_id = $_SESSION['user_id'];

// Connect to database
$conn = mysqli_connect("localhost", "root", "", "roadmaster");

// Check connection
if (!$conn) {
  die("Connection failed: " . mysqli_connect_error());
}

// Query user details from user table
$sql = "SELECT * FROM user WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);

// Check if query was successful
if (!$result) {
  die("Error: " . mysqli_error($conn));
}

// Get user details from query result
$user = mysqli_fetch_assoc($result);

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
  $sql = "SELECT password FROM user WHERE id = '$user_id'";
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
  $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
  $sql = "UPDATE user SET name = '$name', email = '$email', mobile = '$mobile', address = '$address', password = '$hashed_new_password' WHERE id = '$user_id'";
  $result = mysqli_query($conn, $sql);

  // Check if query was successful
  if (!$result) {
    die("Error: " . mysqli_error($conn));
  }

  // Redirect to profile page
  header("Location: uprofile.php");
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
      <a class="navbar-item" href="userdash.php">
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
            <a class="button yellow" href="logout.php">
              Log out
            </a>          
        </div>
      </div>
    </div>
  </nav>
  <section class="section">
    <div class="container">
      <h1 class="title">Edit Profile</h1>
      <div class="columns">
        <div class="column is-half">
          <form method="post">
            <div class="field">
              <label class="label">Name</label>
              <div class="control">
                <input class="input" type="text" name="name" value="<?php echo $user['name']; ?>" required>
              </div>
            </div>
            <div class="field">
              <label class="label">Email</label>
              <div class="control">
                <input class="input" type="email" name="email" value="<?php echo $user['email']; ?>" required>
              </div>
            </div>
            <div class="field">
              <label class="label">Mobile Number</label>
              <div class="control">
                <input class="input" type="tel" name="mobile" value="<?php echo $user['mobile']; ?>" required>
              </div>
            </div>
            <div class="field">
              <label class="label">Address</label>
              <div class="control">
                <textarea class="textarea" name="address" required><?php echo $user['address']; ?></textarea>
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
                <a class="button is-light" href="profile.php">Cancel</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
</body>
</html>