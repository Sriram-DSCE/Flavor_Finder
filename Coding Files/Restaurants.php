<?php
include '../includes/db.php';

// Fetch restaurants with recommendation score, cuisine and location
$sql = "SELECT rr.RestaurantID,
               rr.RestaurantName,
               rr.Address,
               rr.ReviewCount,
               rr.AverageRating,
               rr.RecommendationScore,
               c.CuisineName,
               l.LocationName
        FROM restaurant_recommendations rr
        JOIN Restaurants r ON rr.RestaurantID = r.ID
        JOIN Cuisines c ON r.CuisineID = c.ID
        JOIN Locations l ON r.LocationID = l.ID
        ORDER BY rr.RecommendationScore DESC";

$result = $conn->query($sql);

$topRatedSql = "SELECT r.Name AS RestaurantName,
                       r.Address,
                       AVG(rv.Rating) AS AverageRating,
                       COUNT(rv.ID) AS ReviewCount
                FROM Restaurants r
                JOIN Reviews rv ON r.ID = rv.RestaurantID
                WHERE rv.Rating >= 4
                GROUP BY r.ID, r.Name, r.Address
                ORDER BY AverageRating DESC";

$topRatedResult = $conn->query($topRatedSql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Restaurants</title>
    <link rel="stylesheet" href="Restaurants.css">
</head>
<body>

    <div class="restaurants-container">

        <h2>Recommended Restaurants</h2>

        <div class="restaurants-grid">

            <?php

            
            if ($result && $result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

                    echo '<div class="restaurant-card">';

                    echo '<h3>🍽️ ' .
                         htmlspecialchars($row["RestaurantName"]) .
                         '</h3>';

                    echo '<p><strong>📍 Location:</strong> ' .
                         htmlspecialchars($row["LocationName"]) .
                         '</p>';

                    echo '<p><strong>🍛 Cuisine:</strong> ' .
                         htmlspecialchars($row["CuisineName"]) .
                         '</p>';

                    echo '<p><strong>🏠 Address:</strong> ' .
                         htmlspecialchars($row["Address"]) .
                         '</p>';

                    echo '<p class="rating">⭐ Average Rating: ' .
                         htmlspecialchars($row["AverageRating"]) .
                         '/5</p>';

                    echo '<p>📝 Reviews: ' .
                         htmlspecialchars($row["ReviewCount"]) .
                         '</p>';

                    echo '<p>🏆 Recommendation Score: ' .
                         htmlspecialchars($row["RecommendationScore"]) .
                         '</p>';

                    echo '<form action="add_favorite.php" method="POST">';
                    echo '<input type="hidden" name="restaurant_id" value="' . htmlspecialchars($row["RestaurantID"]) . '">';
                    echo '<button type="submit">❤️ Add to Favorites</button>';
                    echo '</form>';

                    echo '</div>';
                }

            } else {

                echo "<p>No restaurants found.</p>";
            }
            ?>

        </div>
    </div>

</body>
</html>

<?php
$conn->close();
?>