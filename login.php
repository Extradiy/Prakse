<?php
ob_start();
session_start();
include 'prakseform.php';
$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    if (empty($email)) {
        $errors["email"] = "Email is required";
    }

    if (empty($password)) {
        $errors["password"] = "Password is required";
    }

    if(empty($errors)) {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);


        if($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user["password"])) {
                $_SESSION["user_id"] = $user["user_id"];
                $_SESSION["username"] = $user["username"];
                header("Location: logged.php");
                exit();
            } else {
                $errors["login"] = "Invalid email or password";
            }
        } else {
            $errors["login"] = "Invalid email or password";
        }
    }


}
 


ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form action="" method="POST">
            <div>
                <label for="email">email:</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" require>
                <span class="error">* <?php echo $errors["email"] ?? '';?></span>
            </div>
            <div>
                <label for="password">password:</label>
                <input type="password" name="password" id="password" placeholder="Enter your password" require>
                <span class="error">* <?php echo $errors["password"] ?? '';?></span>
            </div>
        <div>
            <span class="error"><?php echo $errors["login"] ?? ""; ?></span>
        </div>
            <div>
                <button type="submit">Login</button>
            </div>
        </form>
</body>
</html>