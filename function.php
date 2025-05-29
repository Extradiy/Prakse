<?php
include 'prakseform.php';
$errors = [];
$first_name = $last_name = $email = $password = $confirm_password = "";
$conn = mysqli_connect("localhost", "root", "", "datubaze_2"); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["first_name"])) {
    $errors["first_name"] = "Name is required";
  } else {
    $first_name = test_input($_POST["first_name"]);
    if (!preg_match("/^[\p{L}' -]*$/u", $first_name)) {
      $errors["first_name"] = "Only letters and white space allowed";
      }
    if(strlen($first_name) < 3){
            $errors["first_name"] = "first_name is too short";
        }
    
  }

if (empty($_POST["last_name"])) {
    $errors["last_name"] = "last_name is required";
  } else {
    $last_name = test_input($_POST["last_name"]);
    if (!preg_match("/^[\p{L}' -]*$/u", $first_name)) {
      $errors["last_name"] = "Only letters and white space allowed";
    }
    if(strlen($last_name) < 3){
        $errors["last_name"] = "last_name is too short";
    }
}


  if (empty($_POST["email"])) {
    $errors["email"] = "Email is required";
  } else {
    $email = test_input($_POST["email"]);
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors["email"] = "Invalid email format";
    }
    $select = mysqli_query($conn, "SELECT `email` FROM `users` WHERE `email` = '".$_POST['email']."'") or exit(mysqli_error($conn));
if(mysqli_num_rows($select)) {
    $errors["email"]= "Email is already used"; }
}

  if (empty($_POST["password"])) {
    $errors["password"] = "Password is required";
  } else {
    $password = test_input($_POST["password"]);
    if(!preg_match('/^(?=.*\d)(?=.*\p{L}).{8,}$/u', $password)) {
      $errors["password"] = 'the password does not meet the requirements!';
    }
    if(strlen($password) < 8) {
        $errors["password"] = "The password is too short"; 
    }
  }

  if (empty($_POST["confirm_password"])) {
    $errors["confirm_password"] = "Confirm is password required";
  } else {
   $confirm_password = test_input($_POST["confirm_password"]);
  }

  if ($_POST["password"] === $_POST["confirm_password"]) {
  } 
  else {
    $errors["confirm_password"]  = "The passwords aren't the same";
  }

 $theGoodHashedPassword = password_hash($password, PASSWORD_DEFAULT);


  if(password_verify($password, $theGoodHashedPassword)){
    echo "";
  } else {
    echo "";
  }


 

  if (empty($errors)) {
      $conn = mysqli_connect("localhost", "root", "", "datubaze_2");

    
    if (mysqli_connect_errno()) {
      echo "failed to connect to sqli:" . mysqli_connect_error();
      exit();
    }


if ($result = mysqli_query($conn, "INSERT INTO users (username, last_name, email, password) VALUES ('$first_name', '$last_name', '$email', '$theGoodHashedPassword')")) {
     header("Location: logged.php");
                exit();
   }
  
  } 

}
        function test_input($data) {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
       ?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form</title>
    <link rel="stylesheet" href="stylesheet.css">
    <style>
        .error {color: #FF0000;}    
    </style>
</head>
<body class="bg-body">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="custom-card shadow-lg p-5 rounded-4">
    <h2 class="text-center mb-4 fw-bold text-primary">Create an Account</h2>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="first_name" class="form-label">First Name</label>
        <input type="text" class="form-control form-control-lg" name="first_name" id="first_name" value="<?= htmlspecialchars($first_name) ?>" placeholder="e.g., John">
        <div class="error"><?= $errors["first_name"] ?? '' ?></div>
      </div>

      <div class="mb-3">
        <label for="last_name" class="form-label">Last Name</label>
        <input type="text" class="form-control form-control-lg" name="last_name" id="last_name" value="<?= htmlspecialchars($last_name) ?>" placeholder="e.g., Doe">
        <div class="error"><?= $errors["last_name"] ?? '' ?></div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" class="form-control form-control-lg" name="email" id="email" value="<?= htmlspecialchars($email) ?>" placeholder="name@example.com">
        <div class="error"><?= $errors["email"] ?? '' ?></div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="At least 8 characters">
        <div class="error"><?= $errors["password"] ?? '' ?></div>
      </div>

      <div class="mb-4">
        <label for="confirm_password" class="form-label">Confirm Password</label>
        <input type="password" class="form-control form-control-lg" name="confirm_password" id="confirm_password" placeholder="Repeat your password">
        <div class="error"><?= $errors["confirm_password"] ?? '' ?></div>
      </div>

      <button type="submit" class="btn btn-primary w-100 btn-lg rounded-3">Register</button>
    </form>
  </div>
</div>

</body>
</html>