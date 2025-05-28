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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["submit_post"])) {
    $content = trim($_POST["post_content"]);

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO posts (user_id, content) VALUES (?, ?)");
        $stmt->bind_param("is", $_SESSION["user_id"], $content);
        $stmt->execute();

        // Redirect to prevent double submission
        header("Location: logged.php");
        exit();
    }
}

$stmt = $conn->prepare("SELECT u.username, p.content, p.created_at 
                        FROM posts p
                        JOIN users u ON p.user_id = u.user_id
                        ORDER BY p.created_at DESC
                        LIMIT 10");
                      
$stmt->execute();
$posts_result = $stmt->get_result();




?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <div class="container">
    <div class="profile-section">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        <img src="uploads/<?php echo htmlspecialchars($profilePic); ?>" alt="Profile Picture" width="100" height="100">
        <p><a href="edit_profile.php">Edit Profile</a></p>
        <p><a href="logout.php">Logout</a></p>
    </div>

    <form action="logged.php" method="POST">
    <textarea name="post_content" rows="4" cols="50" placeholder="What's on your mind?" required></textarea><br>
    <button type="submit" name="submit_post">Post</button>
</form>

    <div class="posts-section">
        <h2>Recent Posts</h2>
        <?php while ($post = $posts_result->fetch_assoc()): ?>
            <div class="post">
                <div class="author"><?php echo htmlspecialchars($post['username']); ?></div>
                <div class="time"><?php echo htmlspecialchars($post['created_at']); ?></div>
                <div class="content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
            </div>
        <?php endwhile; ?>
    </div>

    
</div>
</body>
</html>