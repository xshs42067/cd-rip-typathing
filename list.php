<?php
// connect to SQL cd_system database!
$conn = new mysqli("localhost", "root", "", "cd_system");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// list artists and their albums
$artist_id = isset($_GET['artist']) ? (int)$_GET['artist'] : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CD Archive - Collection</title>
<!--Montserrat font, download-->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        audio {
            width: 100%;
            height: 30px;
            margin-top: 10px;
            filter: grayscale(1) contrast(1.2) invert(1);
        }
        .song-item {
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }
        .song-item:last-child { border-bottom: none; }
        .track-info {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .track-num { color: #ff6fff; font-weight: 800; margin-right: 10px; }
    </style>
</head>
<body>

<!-- navbar -->
<nav>
    <h2>🎵 CD Archive</h2>
    <div class="nav-links">
        <a href="index.php">Home</a>
        <a href="list.php">List</a>
        <a href="add.php">Add</a>
    </div>
</nav>

<main class="container">
    <?php if (!$artist_id): ?>
        <h1>Ripped Archives</h1>
        <p style="margin-bottom: 30px; color: #888;">Select an artist to view their albums.</p>
        
        <!--lists all artists and display as cards-->
        <div class="grid">
            <?php
            $artists = $conn->query("SELECT * FROM Artist ORDER BY artist_name ASC");
            if ($artists->num_rows > 0): // check if there are artists in the database
              // loop through artists and display as cards
                while($a = $artists->fetch_assoc()): ?> 
                    <a href="list.php?artist=<?php echo $a['artist_id']; ?>" class="card artist-card" style="text-decoration: none;">
                        <div class="card-content">
                            <p class="album-title">Artist</p>
                            <h3><?php echo htmlspecialchars($a['artist_name']); ?></h3>
                        </div>
                    </a>
                <?php endwhile;
            else: ?> <!-- if artists empty -->
                <div class="empty-state"><h2>Archive is Empty</h2> <a href="add.php" class="btn">Start Ripping CDs</a></div>
            <?php endif; ?>
        </div>

    <?php else: ?>
      <!-- show albums of selected artist!!!! -->
        <?php 
            $artist_res = $conn->query("SELECT artist_name FROM Artist WHERE artist_id = $artist_id");
            $artist_data = $artist_res->fetch_assoc();
        ?>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1><?php echo htmlspecialchars($artist_data['artist_name']); ?></h1>
            <a href="list.php" class="btn" style="padding: 10px 20px; font-size: 0.8rem;">← Back to Artists</a>
        </div>
        <!-- list albums of this artist -->
        <div class="grid">
            <?php
            $albums = $conn->query("SELECT * FROM CD_Album WHERE artist_id = $artist_id ORDER BY release_year DESC");
            while($alb = $albums->fetch_assoc()): ?>
                <div class="card">
                    <?php 
                        $img = (!empty($alb['album_art']) && file_exists("uploads/".$alb['album_art'])) 
                               ? "uploads/".$alb['album_art'] : "images/noimage.png";
                    ?>
                    <img src="<?php echo $img; ?>" class="card-img" alt="Cover">
                    
                    <div class="card-content">
                        <h3><?php echo htmlspecialchars($alb['album_title']); ?></h3> <h4 class="artistname"><?php echo htmlspecialchars($artist_data['artist_name']); ?></h4>
                        <p class="meta-data"><?php echo $alb['release_year']; ?> • <?php echo $alb['condition_status']; ?></p>
                        <p class="meta-data">Acquired: <?php echo $alb['date_acquired']; ?></p>
                        
                         <!-- list songs of this album -->
                        
                        <div class="track-list" style="margin-top: 20px;">
                            <?php 
                            $songs = $conn->query("SELECT * FROM Song WHERE album_id = ".$alb['album_id']." ORDER BY track_number ASC");
                            while($s = $songs->fetch_assoc()): ?>
                                <div class="song-item">
                                    <div class="track-info">
                                        <span><span class="track-num">#<?php echo $s['track_number']; ?></span> <?php echo htmlspecialchars($s['song_title']); ?></span>
                                    </div>
                                    
                                    <?php 
                                    $file_path = "music/" . $s['song_file'];
                                    if (!empty($s['song_file']) && file_exists($file_path)): ?>
                                        <audio controls controlsList="nodownload">
                                            <source src="<?php echo $file_path; ?>" type="audio/mpeg">
                                            <source src="<?php echo $file_path; ?>" type="audio/wav">
                                            Your browser does not support audio.x
                                        </audio>
                                    <?php else: ?>
                                        <small style="color: #cc0000; font-style: italic;">File missing in /music/ folder</small>
                                    <?php endif; ?>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</main>
<!-- footer-->
<footer>
  <p>Made by the Group 2 of BSIT-1A for Fundamentals of Database Systems :3</p>
</footer>
</body>
</html>