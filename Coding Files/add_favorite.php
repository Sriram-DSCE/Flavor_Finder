<?php

include '../includes/db.php';
session_start();

// Check whether the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Get the restaurant ID from the form
$restaurant_id = $_POST['restaurant_id'];

// Get the logged-in user's ID
$username = $_SESSION['username'];

$sql = "SELECT ID FROM users WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Check whether the user ID was found
if (!$user_id) {
    die("User not found.");
}

// Add restaurant to favorites
$sql = "INSERT IGNORE INTO favorites (UserID, RestaurantID)
        VALUES (?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $restaurant_id);
$stmt->execute();

$stmt->close();
$conn->close();

// Go back to restaurants page
header("Location: restaurants.php");
exit();

?>