<?php

include "db.php";
// get artist id, defaults to null when empty
$artist_id = isset($_GET['artist']) ? (int)$_GET['artist'] : null;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CDRip - Ripped Albums</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
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
    <?php if (!$artist_id): ?>
        <!-- select artist -->
        <h1>Ripped Archives</h1>
        <p style="margin-bottom: 30px; color: #888;">Select an artist to view their albums.</p>
        <div class="grid">
            <?php
            $artists = $conn->query("SELECT * FROM Artist ORDER BY artist_name ASC");
            if ($artists->num_rows > 0):
                while ($a = $artists->fetch_assoc()): ?>
                    <a href="list.php?artist=<?php echo $a['artist_id']; ?>" class="card artist-card" style="text-decoration:none;">
                        <div class="card-content">
                            <p class="album-title">Artist</p>
                            <h3><?php echo htmlspecialchars($a['artist_name']); ?></h3>
                        </div>
                    </a>
                <?php endwhile;
            // if no artists in database, SHOW THIS! VVVV
            else: ?> 
                <div class="empty-state"><h2>Archive is Empty</h2><a href="add.php" class="btn">Start Storing CD Albums</a></div>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- list albums for selected artist -->
        <?php 
        // get artist name for header
        $artist_res = $conn->query("SELECT artist_name FROM Artist WHERE artist_id = $artist_id");
        $artist_data = $artist_res->fetch_assoc();
        ?>
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1><?php echo htmlspecialchars($artist_data['artist_name']); ?></h1> <!-- artist name header thing -->
            <a href="list.php" class="btn" style="padding:10px 20px; font-size:0.8rem;">← Back to Artists</a>
        </div>
        <div class="grid">
            <?php
            // get albums for selected artist
            $albums = $conn->query("SELECT * FROM CD_Album WHERE artist_id = $artist_id ORDER BY release_year DESC");
            while ($alb = $albums->fetch_assoc()):
                $img = (!empty($alb['album_art']) && file_exists('uploads/' . $alb['album_art'])) ? 'uploads/' . $alb['album_art'] : 'images/noimage.png';
            ?>
                <div class="card">
                    <img src="<?php echo $img; ?>" class="card-img" alt="Cover">
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($alb['album_title']); ?></h3>
                        <h4 class="artistname"><?php echo htmlspecialchars($artist_data['artist_name']); ?></h4>
                        <p class="meta-data"><?php echo $alb['release_year']; ?> • <?php echo $alb['condition_status']; ?></p>
                        <p class="meta-data">Acquired: <?php echo htmlspecialchars($alb['date_acquired']); ?></p>
                        <div class="track-list" style="margin-top:20px;">
                            <!-- track list -->
                            <?php
                            // get songs for album
                            $songs = $conn->query("SELECT * FROM Song WHERE album_id = " . $alb['album_id'] . " ORDER BY track_number ASC");
                            if ($songs->num_rows > 0): ?>
                                <ul>
                                <?php while ($s = $songs->fetch_assoc()): ?>
                                    <li>
                                        <span><?php echo $s['track_number']; ?></span>
                                        <?php echo htmlspecialchars($s['song_title']); ?>
                                        <?php $file_path = 'music/' . $s['song_file']; ?>
                                        <?php if (!empty($s['song_file']) && file_exists($file_path)): ?>
                                            <audio controls controlsList="nodownload" style="margin-top:10px; width:100%; display:block;">
                                                <source src="<?php echo $file_path; ?>" type="audio/mpeg">
                                                <source src="<?php echo $file_path; ?>" type="audio/wav">
                                                Your browser does not support audio.
                                            </audio>
                                        <?php else: ?>
                                            <small style="color:#cc0000; display:block; margin-top:8px; font-style:italic;">File missing in /music/ folder</small>
                                        <?php endif; ?>
                                    </li>
                                <?php endwhile; ?>
                                </ul>
                            <?php else: ?> <!-- no songs?! -->
                                <p style="color:#888; font-size:0.9rem;">No tracks found for this album.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>
<footer>
    <p>Made by the Group 2 of BSIT-1A for Fundamentals of Database Systems :3</p>
</footer>
</body>
</html>
