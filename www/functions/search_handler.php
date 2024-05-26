<?php
/**
 * Author: Yudum Yilmaz
 * Last modified on: 12.05.2024
 * Title: Search Handler
 * Summary: This page displays search results or filtered series based on 
 * user input such as search query OR initial letter, genre, and platform. 
 * Series from the default library are displayed here.
 * Filters and search inputs only apply to the user's own series.
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit();
}

// Include the database connection
include "../functions/db_connection.php";

$searchQuery = isset($_GET['query']) ? $_GET['query'] : '';
$genreFilter = isset($_GET['genre']) ? $_GET['genre'] : 'genre';
$platformFilter = isset($_GET['platform']) ? $_GET['platform'] : 'platform';
$paramTypes = '';
$paramValues = array();

// SQL query for user-added series
$userSql = "SELECT * FROM my_series WHERE 1=1";

$titleFilter = isset($_GET['title']) ? $_GET['title'] : 'title';

if ($titleFilter != 'title') {
    // Title filter for user-added series
    switch ($titleFilter) {
        case 'a-j':
            $userSql .= " AND title REGEXP '^[a-j]'";
            break;
        case 'k-t':
            $userSql .= " AND title REGEXP '^[k-t]'";
            break;
        case 'u-z':
            $userSql .= " AND title REGEXP '^[u-z]'";
            break;
    }
}

if ($searchQuery != '') {
    // Search query to SQL statement for user-added series
    $userSql .= " AND title LIKE '%$searchQuery%'";
}

if ($genreFilter != 'genre') {
    // Genre filter for user-added series
    $userSql .= " AND genre = ?";
    $paramTypes .= 's';
    $paramValues[] = $genreFilter;
}

if ($platformFilter != 'platform') {
    // Platform filter for user-added series
    $userSql .= " AND platform = ?";
    $paramTypes .= 's';
    $paramValues[] = $platformFilter;
}

// Prepare and execute SQL query for user-added series
$userStmt = mysqli_prepare($conn, $userSql);
if ($userStmt) {
    // Bind parameters
    if (!empty($paramTypes)) {
        mysqli_stmt_bind_param($userStmt, $paramTypes, ...$paramValues);
    }
    
    mysqli_stmt_execute($userStmt);

    $userResult = mysqli_stmt_get_result($userStmt);

    // Display user-added series
    if (mysqli_num_rows($userResult) > 0) {
        echo "<div class='my-series-container'>";
        $count = 0;
        while ($row = mysqli_fetch_assoc($userResult)) {
            if ($count % 2 == 0) {
                echo "<div class='search-results-row'>";
            }
            echo "<div class='search-result'>";
            echo "<img src='data:image/jpg;base64," . base64_encode($row['picture']) . "' alt='" . $row['title'] . "'>";
            echo "<h2>" . $row['title'] . "</h2>";
            echo "<p><strong>Genre:</strong> " . $row['genre'] . "</p>";
            echo "<p><strong>Platform:</strong> " . $row['platform'] . "</p>";
            echo "<p><strong>Seasons:</strong> " . $row['seasons'] . "</p>";
            echo "</div>";
            if ($count % 2 != 0) {
                echo "</div>";
            }
            $count++;
        }
        if ($count % 2 != 0) {
            echo "</div>";
        }
    } else {
        echo "0 results";
    }
    echo "</div>";
}

// SQL query for default series
$defaultSql = "SELECT * FROM default_series";

$stmt = mysqli_prepare($conn, $defaultSql);
if ($stmt) {
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Display results from default_series
    if (mysqli_num_rows($result) > 0) {
        echo "<div class='default-series-container'>";
        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            if ($count % 2 == 0) {
                echo "<div class='search-results-row'>";
            }
            echo "<div class='search-result'>";
            echo "<img src='" . $row['picture_url'] . "' alt='" . $row['title'] . "'>";
            echo "<h2>" . $row['title'] . "</h2>";
            echo "<p><strong>Genre:</strong> " . $row['genre'] . "</p>";
            echo "<p><strong>Platform:</strong> " . $row['platform'] . "</p>";
            echo "<p><strong>Seasons:</strong> " . $row['seasons'] . "</p>";
            echo "<form action='../functions/add_to_my_series.php' method='POST'>";
            echo "<input type='hidden' name='default_series_id' value='" . $row['id'] . "'>";
            echo "<button type='submit' class='add-to-my-series-button' title='Add series to My Series'>+</button>";
            echo "</form>";
            echo "</div>";
            if ($count % 2 != 0) {
                echo "</div>";
            }
            $count++;
        }
        if ($count % 2 != 0) {
            echo "</div>";
        }
        echo "</div>";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

mysqli_close($conn);
?>