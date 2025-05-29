<?php
session_start();
include "function.php"; 
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
        header("Location: index.php");
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
<body class="bg-body">

<!-- Top Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
  <div class="container-fluid">
    <span class="navbar-brand fw-bold text-primary"></span>
    <div class="d-flex ms-auto">
      <a href="edit_profile.php" class="btn btn-outline-primary me-2">Edit Profile</a>
      <a href="logout.php" class="btn btn-outline-danger">Logout</a>
    </div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row min-vh-100">

    
    <div class="col-md-3 bg-white shadow-sm p-4">
      <div class="text-center mb-4">
        <img src="uploads/<?= htmlspecialchars($profilePic); ?>" class="rounded-circle img-fluid mb-2" width="100" height="100" alt="Profile Picture">
        <h5 class="fw-bold"><?= htmlspecialchars($_SESSION["username"]); ?></h5>
      </div>
    </div>

    
    <div class="col-md-9 p-5">
      <div class="mb-4">
        <h2 class="text-primary">Create a Post</h2>
        <form action="logged.php" method="POST">
          <div class="mb-3">
            <textarea name="post_content" rows="4" class="form-control" placeholder="What's on your mind?" required></textarea>
          </div>
          <button type="submit" name="submit_post" class="btn btn-primary">Post</button>
        </form>
      </div>

      <hr>

      <div>
        <h3 class="mb-4">Recent Posts</h3>
        <?php while ($post = $posts_result->fetch_assoc()): ?>
          <div class="card mb-3 shadow-sm">
            <div class="card-body">
              <h5 class="card-title text-primary"><?= htmlspecialchars($post['username']); ?></h5>
              <h6 class="card-subtitle mb-2 text-muted"><?= htmlspecialchars($post['created_at']); ?></h6>
              <p class="card-text"><?= nl2br(htmlspecialchars($post['content'])); ?></p>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>

  </div>
</div>

</body>
</html>