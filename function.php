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
    if (!preg_match("/^[a-zA-Z-' ]*$/",$first_name)) {
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
    if(!preg_match("/^[a-zA-Z-' ]*$/",$last_name)) {
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
    if(!preg_match('/^(?=.*\d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,}$/', $password)) {
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
<body>
    <div class="container">
        <h2>Reģistrācijas Forma</h2>
        <form action="" method="POST">
            <div>
                <label for="first_name">first_name:</label>
                <input type="text" name="first_name" id="first_name" placeholder="Enter your first_name">
                <span class="error">* <?php echo $errors["first_name"] ?? '';?></span>
            </div>
            <div>
                <label for="last_name">last_name:</label>
                <input type="text" name="last_name" id="last_name" placeholder="Enter your first_name">
                <span class="error">* <?php echo $errors["last_name"] ?? '';?></span>
            </div>
            <div>
                <label for="email">email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your email">
                <span class="error">* <?php echo $errors["email"] ?? '';?></span>
            </div>
            <div>
                <label for="password">password:</label>
                <input type="password" name="password" id="password" placeholder="Enter your password">
                <span class="error">* <?php echo $errors["password"] ?? '';?></span>
            </div>
            <div>
                <label for="confirm_password">confirm_password:</label>
                <input type="password" name="confirm_password" id="confirm_password" placeholder="Enter your repeatpassword">
                <span class="error">* <?php echo $errors["confirm_password"] ?? '';?></span>
            </div>
            <div>
                <button type="submit">Register</button>
            </div>


</html>