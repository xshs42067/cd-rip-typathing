<?php
include "db.php";

$existing_albums = $conn->query("SELECT a.album_id, a.album_title, art.artist_name FROM CD_Album a JOIN Artist art ON a.artist_id = art.artist_id ORDER BY a.album_title ASC");
$existing_songs = $conn->query("SELECT s.song_id, s.song_title, a.album_title, art.artist_name FROM Song s JOIN CD_Album a ON s.album_id = a.album_id JOIN Artist art ON a.artist_id = art.artist_id ORDER BY art.artist_name, a.album_title, s.track_number ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CDRip - Modify</title>
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
    <div class="form-wrapper">
        <h1>Modify CD Archive</h1>
        <form method="POST" action="actions/insert.php" enctype="multipart/form-data">
            <div class="input-group">
                <label>Mode</label>
                <select name="mode" id="mode" class="select-style" onchange="toggleFields()">
                    <!--<input type="radio" name="mode" value="new" checked>New Album</input> (((((i tried changing the options into a radio button instead, it broke. nevermind. -hans, circa 4/20/26)))))-->
                    <option value="new">New Album</option>
                    <option value="existing">Add Track to Existing Album</option>
                    <option value="delete">Delete Track</option>
                    <option value="del_album">Delete Album</option>
                </select>
            </div>

        <!-- add -->

        <div id="newAlbumFields">
            <div class="input-group"><label>Artist Name</label><input type="text" name="artist_name" required></div>
            <div class="input-group"><label>Album Title</label><input type="text" name="album_title" required></div>
            <div class="input-group"><label>Year</label><input type="number" name="release_year" required></div>
            <div class="input-group"><label>Date Acquired</label><input type="date" name="date_acquired" value="<?php echo date('Y-m-d'); ?>" required></div>
            <div class="input-group"><label>Condition</label>
                <select name="condition_status" class="select-style">
                    <option value="Brand New">Brand New</option>
                    <option value="Used (Good Condition)">Used (Good Condition)</option>
                    <option value="Used (Poor Condition)">Used (Poor Condition)</option>
                    <option value="Unusable (Badly Scratched)">Unusable (Badly Scratched)</option>
                </select>
            </div>
            <div class="input-group"><label>Album Cover</label><input type="file" name="album_art" accept="image/*"></div>
        </div>

        <!-- add to existing -->

        <div id="existingAlbumFields" style="display:none;">
            <div class="input-group"><label>Select Existing Album</label>
                <select name="existing_album_id" class="select-style">
                    <?php if ($existing_albums->num_rows > 0): ?> 
                        // if else statement so itll display "no albums added, dum!" instead of just pure empty dropdown menu
                        <?php while ($row = $existing_albums->fetch_assoc()): ?>
                            <option value="<?php echo $row['album_id']; ?>"><?php echo htmlspecialchars($row['album_title'] . ' - ' . $row['artist_name']); ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option disabled selected>No albums are added... yet!</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!--delete a song-->

        <div id="deleteFields" style="display:none;">
            <div class="input-group"><label>Select Track to Delete</label>
                <select name="delete_song_id" class="select-style">
                    <?php if ($existing_songs->num_rows > 0): ?> 
                        // if else statement so itll display "no songs added, dum!!!@#!@#!23" instead of just pure empty dropdown menu
                        // btw this is the dropdown menu for deletion
                        <?php while ($row = $existing_songs->fetch_assoc()): ?>
                            <option value="<?php echo $row['song_id']; ?>"><?php echo htmlspecialchars($row['artist_name'] . ' - ' . $row['album_title'] . ' - ' . $row['song_title']); ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option disabled selected>No songs are added</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!--delete album-->
        
        <div id="deleteAlbumFields" style="display:none;">
            <div class="input-group"><label>Select Album to Delete</label>
                <select name="delete_album_id" class="select-style">
                    <?php
                    // reusing existing albums variable since it has the same data as the dropdown for adding to existing albums, just with different display text
                    $existing_albums->data_seek(0); // reset pointer to reuse result set
                    if ($existing_albums->num_rows > 0): ?> 
                        <?php while ($row = $existing_albums->fetch_assoc()): ?>
                            <option value="<?php echo $row['album_id']; ?>"><?php echo htmlspecialchars($row['artist_name'] . ' - ' . $row['album_title']); ?></option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option disabled selected>No albums are added... yet!</option>
                    <?php endif; ?>
                </select>
            </div>
        </div>

        <!-- for adding and adding to existing album im too lazy and also trying to make stuff modular bleh-->
        <div id="songFields">
            <div class="input-group"><label>Song Title</label><input type="text" name="song_title" required></div>
            <div class="input-group"><label>Track #</label><input type="number" name="track_number" required></div>
            <div class="input-group"><label>Audio File</label><input type="file" name="music_file" accept="audio/*" required></div>
            <button type="submit" class="btn-submit">Save</button>
        </div>

        <!-- OKAY SO, FOR SOME REASON NAGA KADTO ANG FORM SINI INTO THE INSERT PHP FILE. SO I USED "this.form.action" (thanks random youtuber) PARA HINDI MAG CONFLICT KAG MAKADTO DIRECTA ANG FORM DESTINATION SAAAAA delete.php........ okay panyapon nako -snah 9:12pm 4/23/26-->
        <div id="deleteButton" style="display:none;">
            <button type="submit" class="btn-submit" onclick="this.form.action='actions/delete.php';">Delete Track</button>
        </div>
        
        <div id="deleteAlbumButton" style="display:none;">
            <button type="submit" class="btn-submit" onclick="this.form.action='actions/delete.php';">Delete Album</button>

        </form>
    </div>
</main>
<footer>
    <p>Made by the Group 2 of BSIT-1A for Fundamentals of Database Systems :3</p>
</footer>

<script src="actions/forms.js"></script>
</body>
</html>