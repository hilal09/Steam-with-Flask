<?php

/**
 * Author: Yudum Yilmaz
 * Last modified on: 12.05.2024
 * Title: Search Page
 * Summary: This page allows users to search for series by title, genre, and platform.
 * Search results are displayed dynamically without reloading the page.
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../pages/index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search</title>
    <link rel="stylesheet" href="../style/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Space+Mono:ital,wght@0,400;0,700;1,400;1,700&family=Urbanist:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <script>
        function loadSearchResults(query, titleFilter, genreFilter, platformFilter) {
            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("search-results").innerHTML = this.responseText;
                } else {
                    console.error("Fehler beim Laden der Suchergebnisse.");
                }
            };
            xhr.open("GET", "../functions/search_handler.php?query=" + query + "&title=" + titleFilter + "&genre=" + genreFilter + "&platform=" + platformFilter, true);
            xhr.send();
        }

        window.onload = function() {
            loadSearchResults("", "title", "genre", "platform");
        }

        document.addEventListener("DOMContentLoaded", function() {
            var searchForm = document.querySelector('.search-form form');
            if (searchForm) {
                searchForm.addEventListener('submit', function(event) {
                    event.preventDefault();
                    var query = document.querySelector('.search-form input[name="query"]').value;
                    var titleFilter = document.querySelector('.filter-options select[name="title"]').value;
                    var genreFilter = document.querySelector('.filter-options select[name="genre"]').value;
                    var platformFilter = document.querySelector('.filter-options select[name="platform"]').value;
                    loadSearchResults(query, titleFilter, genreFilter, platformFilter);
                });
            }
        });
    </script>
</head>
<body>
    <?php include "../widgets/navigation.php"; ?>
    <?php include "../widgets/logo.php"; ?>
    
    <div class="search-container">
        <div class="search-form">
            <form action="../functions/search_handler.php" method="GET">
                <input type="text" name="query" placeholder="What are you looking for?">
                <button type="submit">Go</button>
            </form>
        </div>

        <div class="filter-options">
            <form action="../functions/search_handler.php" method="GET">
                <select name="title">
                    <option value="title">Title</option>
                    <option value="a-j">A-J</option>
                    <option value="k-t">K-T</option>
                    <option value="u-z">U-Z</option>
                </select>

                <select name="genre">
                    <option value="genre">Genre</option>
                    <option value="action">Action</option>
                    <option value="adventure">Adventure</option>
                    <option value="animation">Animation</option>
                    <option value="adult-swim">Adult Swim</option>
                    <option value="comedy">Comedy</option>
                    <option value="docu">Documentary</option>
                    <option value="drama">Drama</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="horror">Horror</option>
                    <option value="romantic">Romantic</option>
                    <option value="sci-fi">Sci-fi</option>
                    <option value="thriller">Thriller</option>
                    <option value="western">Western</option>
                    <option value="other">Other</option>
                </select>

                <select name="platform">
                    <option value="platform">Platform</option>
                    <option value="amazon prime">Amazon Prime</option>
                    <option value="hbo">HBO</option>
                    <option value="hbo max">HBO Max</option>
                    <option value="hulu">Hulu</option>
                    <option value="netflix">Netflix</option>
                    <option value="nbc">NBC</option>
                    <option value="rtl+">Disney+</option>
                    <option value="rtl+">RTL+</option>
                    <option value="other">Other</option>
                </select>
            </form>
        </div>

        <div class="series-container">
            <div class="my-series-container">
                <div class="table-titles">
                    <h2 class="table-title">My Series</h2>
                </div>
            </div>

            <div class="default-series-container">
                <div class="table-titles">
                    <h2 class="table-title">Default Series</h2>
            </div>

            </div>
        </div>
        <div id="search-results" class="search-results-container"></div>
        
    </div>
</body>
</html>