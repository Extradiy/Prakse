<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

include "prakseform.php";

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

            header("Location: logged.php");
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
<html>
<head>
    <title>Edit Profile</title>
</head>
<body>
    <h1>Edit Profile</h1>
    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form action="edit_profile.php" method="post" enctype="multipart/form-data">
        <label for="profile_pic">Upload New Profile Picture:</label>
        <input type="file" name="profile_pic" id="profile_pic" required>
        <input type="submit" value="Upload">
    </form>
    <p><a href="logged.php">Back to Dashboard</a></p>
</body>
</html>