<?php
// Connect to MySQL database
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'roadmaster';

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get form data and sanitize
$name = mysqli_real_escape_string($conn, $_POST['name']);
$mobile = mysqli_real_escape_string($conn, $_POST['mobilenumber']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$licensenumber = mysqli_real_escape_string($conn, $_POST['licensenumber']);
$password = mysqli_real_escape_string($conn, $_POST['Password_1']);
$confirm_password = mysqli_escape_string($conn, $_POST['Password_2']);

// Check if password and confirm password match
if ($password !== $confirm_password) {
    die("Error: Password and Confirm Password do not match");
}

$address = mysqli_real_escape_string($conn, $_POST['address']);

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle file uploads
$license_path = "";
$photo_path = "";
$license_filename = "";
$photo_filename = "";
$license_tmpname = "";
$photo_tmpname = "";

if (isset($_FILES['license'])) {
    $license_name = $_FILES['license']['name'];
    $license_tmp_name = $_FILES['license']['tmp_name'];
    $license_target_dir = "uploads/";
    $license_target_file = $license_target_dir . $name . "_license." . pathinfo($license_name, PATHINFO_EXTENSION);
    move_uploaded_file($license_tmp_name, $license_target_file);
    $license_path = $license_target_file;
}

if (isset($_FILES['photo'])) {
    $photo_name = $_FILES['photo']['name'];
    $photo_tmp_name = $_FILES['photo']['tmp_name'];
    $photo_target_dir = "uploads/";
    $photo_target_file = $photo_target_dir . $name . "_photo." . pathinfo($photo_name, PATHINFO_EXTENSION);
    move_uploaded_file($photo_tmp_name, $photo_target_file);
    $photo_path = $photo_target_file;
}

// Insert data into MySQL database
$sql = "INSERT INTO driver (name, mobile, email, licensenumber, licensepath, photopath, password, address)
        VALUES ('$name', '$mobile', '$email', '$licensenumber', '$license_path', '$photo_path', '$hashed_password', '$address')";

if (mysqli_query($conn, $sql)) {
    // Registration successful
    echo '<!DOCTYPE html>
    <html>
    <head>
        <title>Registration Successful</title>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/style.css">
        <link rel="stylesheet" href="bulma.css">
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-item" href="index.html">
        <img src="res/logo-no-background.png" class="logo">
      </a>
        </nav>
    
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="alert alert-success text-center" role="alert">
                        <h4 class="alert-heading">Registration Successful!</h4>
                        <p class="mb-3">Welcome to RoadMaster. Your registration has been successful. Thank you for joining us.</p>
                        <a href="login.php" class="btn btn-primary">Go to Login</a>
                    </div>
                </div>
            </div>
        </div>
    
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"
            integrity="sha384-jbksGZTJ8pw1E68BU3qM3SNTXK+v1bkiDlBMLquzlZG4PzE0+cQ2g1ohWY4Y7r/D"
            crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    </body>
    </html>';} 
    else {
        // Registration failed
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
        
        // Close MySQL database connection
        mysqli_close($conn);
        ?>

