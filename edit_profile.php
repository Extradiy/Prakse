<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "function.php";

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["profile_pic"])) {
    $uploadDir = "uploads/";
    $fileName = basename($_FILES["profile_pic"]["name"]);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileExt, $allowed)) {
        $newFileName = uniqid() . "." . $fileExt;
        $targetFile = $uploadDir . $newFileName;

        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $targetFile)) {
          
            $check = $conn->prepare("SELECT * FROM user_profiles WHERE user_id = ?");
            $check->bind_param("i", $_SESSION["user_id"]);
            $check->execute();
            $result = $check->get_result();

            if ($result->num_rows > 0) {
                
                $stmt = $conn->prepare("UPDATE user_profiles SET profile_pic = ? WHERE user_id = ?");
                $stmt->bind_param("si", $newFileName, $_SESSION["user_id"]);
                $stmt->execute();
            } else {
                
                $stmt = $conn->prepare("INSERT INTO user_profiles (user_id, profile_pic) VALUES (?, ?)");
                $stmt->bind_param("is", $_SESSION["user_id"], $newFileName);
                $stmt->execute();
            }

            header("Location: index.php");
            exit();
        } else {
            $error = "Failed to upload file.";
        }
    } else {
        $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rediģēt Profilu</title>
</head>
<body class="bg-body">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="custom-card shadow-lg p-5 rounded-4 w-100" style="max-width: 500px;">
        <h2 class="text-center mb-4 fw-bold text-primary">Rediģēt Profilu</h2>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="profile_pic" class="form-label">Augšupielādēt Jaunu Porfila bildi</label>
                <input class="form-control" type="file" name="profile_pic" id="profile_pic" accept="image/*" required onchange="previewImage(event)">
                <img id="preview" class="preview-img rounded-circle border d-block mx-auto mt-3" style="display: none;">
            </div>
            <button type="submit" class="btn btn-primary w-100 btn-lg rounded-3">Augšupielādēt</button>
            <a href="index.php" class="btn btn-outline-secondary w-100 btn-lg rounded-3 mt-2">Atpakaļ uz Index</a>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('preview');
        output.src = reader.result;
        output.style.display = 'block';
    };
    reader.readAsDataURL(event.target.files[0]);
}
</script>

</body>
</html>