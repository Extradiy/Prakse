<?php
session_start();
include "prakseform.php"; 
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}


$stmt = $conn->prepare("SELECT profile_pic FROM user_profiles WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();


$profilePic = (is_array($profile) && isset($profile["profile_pic"])) ? $profile["profile_pic"] : 'default.png';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>
    <img src="uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" width="100" height="100">
    <p><a href="edit_profile.php">Edit Profile</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>