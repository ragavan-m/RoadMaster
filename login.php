<?php
session_start();
// check if the user has submitted the login form
if (isset($_POST['login'])) {
  // get the entered email and password
  $email = $_POST['email'];
  $password = $_POST['password'];
  
  // determine the login type (user or driver) based on the hidden field value
  $login_type = $_POST['login_type'] ?? 'user';
  
  // perform the database query to check if the user or driver exists
  $conn = mysqli_connect("localhost", "root", "", "roadmaster");
  if ($login_type == 'driver') {
    $query = "SELECT * FROM driver WHERE email = '$email'";
  } else {
    $query = "SELECT * FROM user WHERE email = '$email'";
  }
  $result = mysqli_query($conn, $query);
  
  // check if the query was successful and user/driver exists
  if ($result && mysqli_num_rows($result) > 0) {
    // get the user/driver details from the query result
    $user = mysqli_fetch_assoc($result);
    
    // verify the entered password with the hashed password stored in the database
    if (password_verify($password, $user['password'])) {
      // store the user/driver details in session variables
      if ($login_type == 'driver') {
        $_SESSION['driver_id'] = $user['id'];
        $_SESSION['driver_name'] = $user['name'];
        $_SESSION['driver_email'] = $user['email'];
        $_SESSION['driver_address'] = $user['address'];
        
        // redirect the driver to the driver dashboard
        header("Location: driverdash.php");
        exit();
      } else {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_phone'] = $user['phone'];        
        $_SESSION['user_address'] = $user['address'];        
        
        // redirect the user to the user dashboard
        header("Location: userdash.php");
        exit();
      }
    } else {
      // if the entered password does not match the hashed password in the database, show an error message
      $error_msg = "Invalid email or password. Please try again.";
    }
  } else {
    // if the query failed or user/driver not found, show an error message
    $error_msg = "Invalid email or password. Please try again.";
  }
  
  // close the database connection
  mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" href="res/logo-color.png" type="image/x-icon">
    <link rel="stylesheet" href="bulma.min.css">
    <link rel="stylesheet" href="bulma.css">
    <link rel="stylesheet" type="text/css" href="./login.css">
</head>

<body>
    <nav class="navbar" role="navigation" aria-label="main navigation">
        <div class="navbar-brand">
            <div data-aos="zoom-in" data-aos-duration="1000">
                <a class="navbar-item" href="index.html">
                    <img src="res/logo-no-background.png" class="logo">
                </a>
            </div>
        </div>
        <div id="navbarBasicExample" class="navbar-menu">
        <div class="navbar-end">
          <div class="navbar-item">
            <div class="buttons">
              <div class="dropdown is-hoverable">
                <div class="dropdown-trigger">
                  <a class="button is-primary">
                    <strong>Sign up</strong>
                  </a>
                </div>
                <div class="dropdown-menu">
                  <div class="dropdown-content">
                      <a href="userreg.html" class="dropdown-item">User Signup</a>
                      <a href="driverreg.html" class="dropdown-item">Driver Signup</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>
    <section class="section">
        <div class="container">
            <div class="columns">
                <div class="column is-half is-offset-one-quarter">
                    <div class="card">
                        <div class="card-header">
                            <div class="tabs">
                                <ul>
                                    <li class="is-active" id="user-tab"><a>User Login</a></li>
                                    <li id="driver-tab"><a>Driver Login</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="user-form">
                                <form method="post" name="login-form" action="">
                                    <?php if (isset($error_msg)) { ?>
                                    <div class="notification is-danger"><?php echo $error_msg; ?></div>
                                    <?php } ?>
                                    <div class="field">
                                        <label class="label">Email</label>
                                        <div class="control">
                                            <input class="input" type="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Password</label>
                                        <div class="control">
                                            <input class="input" type="password" name="password" required>
                                        </div>
                                    </div>
                                    <input type="hidden" name="login_type" value="user">
                                    <div class="field is-grouped">
                                        <div class="control">
                                            <button class="button is-link" type="submit" name="login"
                                                value="Login">Login</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="driver-form is-hidden">
                                <form method="post" name="driver-login-form" action="">
                                    <?php if (isset($error_msg)) { ?>
                                    <div class="notification is-danger"><?php echo $error_msg; ?></div>
                                    <?php } ?>
                                    <div class="field">
                                        <label class="label">Email</label>
                                        <div class="control">
                                            <input class="input" type="email" name="email" required>
                                        </div>
                                    </div>
                                    <div class="field">
                                        <label class="label">Password</label>
                                        <div class="control">
                                            <input class="input" type="password" name="password" required>
                                        </div>
                                    </div>
                                    <input type="hidden" name="login_type" value="driver">
                                    <div class="field is-grouped">
                                        <div class="control">
                                            <button class="button is-link" type="submit" name="login"
                                                value="Driver Login">Login</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <script>
    // toggle between user and driver login forms
    const userForm = document.querySelector('.user-form');
    const driverForm = document.querySelector('.driver-form');
    const userTab = document.getElementById('user-tab');
    const driverTab = document.getElementById('driver-tab');
    userTab.addEventListener('click', () => {
        driverForm.classList.add('is-hidden');
        userForm.classList.remove('is-hidden');
        driverTab.classList.remove('is-active');
        userTab.classList.add('is-active');
    });

    driverTab.addEventListener('click', () => {
        userForm.classList.add('is-hidden');
        driverForm.classList.remove('is-hidden');
        userTab.classList.remove('is-active');
        driverTab.classList.add('is-active');
    });
</script>
</body>

</html>