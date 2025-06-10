<?php
ob_start();
session_start();
include 'function.php';
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
                header("Location: index.php");
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
<body class="bg-body">

<div class="container d-flex justify-content-center align-items-center vh-100">
  <div class="custom-card shadow-lg p-5 rounded-4">
    <h2 class="text-center mb-4 fw-bold text-primary">Pierakstīties</h2>
    <form method="POST" action="">
      <div class="mb-3">
        <label for="email" class="form-label">Ēpasta Adresse</label>
          <input type="email" class="form-control form-control-lg" name="email" id="email" placeholder="Ieraksti savu Ēpasta adresi" value="<?= htmlspecialchars($email ?? '') ?>">
        <div class="error"><?= $errors["email"] ?? '' ?></div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Parole</label>
        <input type="password" class="form-control form-control-lg" name="password" id="password" placeholder="Ieraksti savu paroli">
        <div class="error"><?= $errors["password"] ?? '' ?></div>
      </div>

      <div class="mb-3 text-danger text-center fw-semibold">
        <?= $errors["login"] ?? '' ?>
      </div>

      <button type="submit" class="btn btn-primary w-100 btn-lg rounded-3 mb-2">Pieslēgties</button>
<a href="register.php" class="btn btn-outline-secondary w-100 btn-lg rounded-3">Reģistrēties</a>
    </form>
  </div>
</div>

</body>
</html>