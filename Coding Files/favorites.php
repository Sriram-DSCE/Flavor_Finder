<?php

include '../includes/db.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

// Get logged-in user's ID
$sql = "SELECT ID FROM users WHERE Username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($user_id);
$stmt->fetch();
$stmt->close();

// Get user's favorite restaurants
$sql = "SELECT r.ID AS RestaurantID,
               r.Name AS RestaurantName,
               r.Address,
               r.Rating,
               f.CreatedAt
        FROM favorites f
        JOIN restaurants r ON f.RestaurantID = r.ID
        WHERE f.UserID = ?
        ORDER BY f.CreatedAt DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>My Favorites</title>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .favorites-container {
            width: 80%;
            margin: 40px auto;
        }

        .favorites-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .favorite-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 10px;
        }
    </style>

</head>

<body>

<div class="favorites-container">

    <h2>❤️ My Favorite Restaurants</h2>

    <div class="favorites-grid">

        <?php

        if ($result && $result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                echo '<div class="favorite-card">';

                echo '<h3>🍽️ ' .
                     htmlspecialchars($row["RestaurantName"]) .
                     '</h3>';

                echo '<p>🏠 Address: ' .
                     htmlspecialchars($row["Address"]) .
                     '</p>';

                echo '<p>⭐ Rating: ' .
                     htmlspecialchars($row["Rating"]) .
                     '/5</p>';

                echo '<p>❤️ Added to favorites</p>';

                echo '<form action="remove_favorite.php" method="POST">';
                echo '<input type="hidden" name="restaurant_id" value="' . htmlspecialchars($row["RestaurantID"]) . '">';
                echo '<button type="submit">🗑️ Remove from Favorites</button>';
                echo '</form>';

                echo '</div>';
            }

        } else {

            echo '<p>You have no favorite restaurants yet.</p>';
        }

        ?>

    </div>

</div>

</body>

</html>

<?php

$stmt->close();
$conn->close();

?>