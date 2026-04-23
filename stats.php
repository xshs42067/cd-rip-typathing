<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>CDRip - Info</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- navbar -->
<nav>       
  <h2>CDRip</h2>
  <div class="nav-links">
    <a href="index.php">Home</a>
    <a href="list.php">List</a>
    <a href="add.php">Modify</a>
    <a href="stats.php">Info</a>
  </div>
</nav>

<main class="container">
    <h1>CDRip Statistics</h1>

    <?php
    include "db.php";

    // total counts for aggregation thingy
    $total_artists = $conn->query("SELECT COUNT(*) as count FROM Artist")->fetch_assoc()['count'];
    $total_albums = $conn->query("SELECT COUNT(*) as count FROM CD_Album")->fetch_assoc()['count'];
    $total_songs = $conn->query("SELECT COUNT(*) as count FROM Song")->fetch_assoc()['count'];

    // subquerying variables
    $avg_albums = $conn->query("SELECT AVG(album_count) as avg FROM (SELECT COUNT(*) as album_count FROM CD_Album GROUP BY artist_id) as sub")->fetch_assoc()['avg'];
    $avg_songs = $conn->query("SELECT AVG(song_count) as avg FROM (SELECT COUNT(*) as song_count FROM Song GROUP BY album_id) as sub")->fetch_assoc()['avg'];


    echo "<div class='stats-grid'>";
    echo "<div class='stat-card'><h3>Total Artists</h3><p>$total_artists</p></div>";
    echo "<div class='stat-card'><h3>Total Albums</h3><p>$total_albums</p></div>";
    echo "<div class='stat-card'><h3>Total Songs</h3><p>$total_songs</p></div>";
    echo "<div class='stat-card'><h3>Average Albums per Artist</h3><p>" . round($avg_albums, 2) . "</p></div>";
    echo "<div class='stat-card'><h3>Average Songs per Album</h3><p>" . round($avg_songs, 2) . "</p></div>";
    echo "</div>";

    // count artists!!!! using GROUP BY and COUNT
    echo "<section class='stats-section'>";
    echo "<h2>Albums per Artist</h2>";
    echo "<p class='section-desc'>See how many albums each artist has in your collection, sorted by the most prolific artists first.</p>";
    $albums_per_artist = $conn->query("SELECT art.artist_name, COUNT(a.album_id) as album_count FROM Artist art LEFT JOIN CD_Album a ON art.artist_id = a.artist_id GROUP BY art.artist_id ORDER BY album_count DESC");
    echo "<div class='grid'>";
    while ($row = $albums_per_artist->fetch_assoc()) {
        echo "<div class='card artist-stat'>";
        echo "<div class='card-content'>";
        echo "<h3>" . htmlspecialchars($row['artist_name']) . "</h3>";
        echo "<p class='meta-data'>Albums: " . $row['album_count'] . "</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
    echo "</section>";

    // count songs!!!! using GROUP BY and COUNT
    echo "<section class='stats-section'>";
    echo "<h2>Songs per Album</h2>";
    echo "<p class='section-desc'>Discover which albums have the most tracks, ordered from largest to smallest collections.</p>";
    $songs_per_album = $conn->query("SELECT a.album_title, art.artist_name, COUNT(s.song_id) as song_count FROM CD_Album a LEFT JOIN Song s ON a.album_id = s.album_id JOIN Artist art ON a.artist_id = art.artist_id GROUP BY a.album_id ORDER BY song_count DESC");
    echo "<div class='grid'>";
    while ($row = $songs_per_album->fetch_assoc()) {
        echo "<div class='card album-stat'>";
        echo "<div class='card-content'>";
        echo "<h3>" . htmlspecialchars($row['album_title']) . "</h3>";
        echo "<h4 class='artistname'>" . htmlspecialchars($row['artist_name']) . "</h4>";
        echo "<p class='meta-data'>Songs: " . $row['song_count'] . "</p>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
    echo "</section>";
    ?>

</main>

<footer>
  <p>Made by the Group 2 of BSIT-1A for Fundamentals of Database Systems :3</p>
</footer>

</body>
</html>