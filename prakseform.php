<?php

$conn = mysqli_connect("localhost", "root", "", "datubaze_2");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

echo '<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">';
echo '<link href="style.css" rel="stylesheet">'
?>