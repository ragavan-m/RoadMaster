<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="bulma.css">
</head>
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
$mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['Password_3']);
$confirm_password = mysqli_escape_string($conn, $_POST['Password_4']);

// Check if password and confirm password match
if ($password !== $confirm_password) {
    die("Error: Password and Confirm Password do not match");
}

// Hash the password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Handle file uploads
$photo_path = "";
if (isset($_FILES['photoProof']) && $_FILES['photoProof']['error'] == UPLOAD_ERR_OK) {
    $photo_tmp_name = $_FILES['photoProof']['tmp_name'];
    $photo_ext = pathinfo($_FILES['photoProof']['name'], PATHINFO_EXTENSION);
    $photo_filename = $name . '_photo.' . $photo_ext;
    $photo_target_file = "uploads/" . $photo_filename;
    if (!move_uploaded_file($photo_tmp_name, $photo_target_file)) {
        die("Error: Failed to move uploaded file");
    }
    $photo_path = $photo_target_file;
}

// Insert data into MySQL database
$stmt = mysqli_prepare($conn, "INSERT INTO user (name, mobile, email, photopath, password, address)
        VALUES (?, ?, ?, ?, ?, ?)");

mysqli_stmt_bind_param($stmt, "ssssss", $name, $mobile, $email, $photo_path, $hashed_password, $_POST['address']);

if (mysqli_stmt_execute($stmt)) {
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
    </html>';
} else {
    // Registration failed
    echo "Error: " . mysqli_error($conn);
}

// Close MySQL connection
mysqli_close($conn);
?>
