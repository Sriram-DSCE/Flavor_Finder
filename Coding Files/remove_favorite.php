<?php

include '../includes/db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$restaurant_id = $_POST['restaurant_id'];

// Get logged-in user's ID
$sql = "SELECT ID FROM users WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Remove the favorite
$sql = "DELETE FROM favorites
        WHERE UserID = ?
        AND RestaurantID = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $restaurant_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: favorites.php");
exit();

?>