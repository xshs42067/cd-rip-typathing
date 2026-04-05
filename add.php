<?php
// connect to database! mysqli is the constructor to open a connection to MySQL on XAMPP, localhost is the hostname, root is the database username. root is the default MySQL username, then "" is the password basically empty lmao then cd_system is the database name i created
$conn = new mysqli("localhost", "root", "", "cd_system");

// list all existing albums for the dropdown menu, thanks again Copilot
$existing_albums = $conn->query("SELECT a.album_id, a.album_title, art.artist_name FROM CD_Album a JOIN Artist art ON a.artist_id = art.artist_id ORDER BY a.album_title ASC");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $mode = $_POST['add_mode']; 
    $song_title = $conn->real_escape_string($_POST['song_title']);
    $track = (int)$_POST['track_number'];

    // --- ALBUM ---
    if ($mode == 'existing') {
        $album_id = (int)$_POST['existing_album_id'];
        // wait im confused lmfaos
    } else {
        $artist_name = $conn->real_escape_string($_POST['artist_name']);
        $album_title = $conn->real_escape_string($_POST['album_title']);
        $rel_year = (int)$_POST['release_year'];
        $cond = $conn->real_escape_string($_POST['condition_status']);
        // CORRECTED: DATE ACQUIRED VARIABLE ADDED!
        $date_acq = $conn->real_escape_string($_POST['date_acquired']); 

        // artist check
        $res_art = $conn->query("SELECT artist_id FROM Artist WHERE artist_name = '$artist_name'");
        $artist_id = ($res_art->num_rows > 0) ? $res_art->fetch_assoc()['artist_id'] : ($conn->query("INSERT INTO Artist (artist_name) VALUES ('$artist_name')") ? $conn->insert_id : 0);

        // album art upload
        $art_path = "noimage.png";
        if (!empty($_FILES["album_art"]["name"])) {
            $target_art = "uploads/" . time() . "_" . basename($_FILES["album_art"]["name"]);
            if(move_uploaded_file($_FILES["album_art"]["tmp_name"], $target_art)) $art_path = basename($target_art);
        }

        // CORRECTED!!: Added date_acquired to the INSERT query
        $conn->query("INSERT INTO CD_Album (album_title, release_year, condition_status, album_art, artist_id, date_acquired) 
                      VALUES ('$album_title', '$rel_year', '$cond', '$art_path', '$artist_id', '$date_acq')");
        $album_id = $conn->insert_id;
    }

    // --- MUSIC FILE UPLOAD ---
    $song_file_name = "";
    if (!empty($_FILES["music_file"]["name"])) {
        $music_dir = "music/";
        if (!file_exists($music_dir)) { mkdir($music_dir, 0777, true); }
        
        $song_file_name = time() . "_" . basename($_FILES["music_file"]["name"]);
        $target_music = $music_dir . $song_file_name;
        
        move_uploaded_file($_FILES["music_file"]["tmp_name"], $target_music);
    }

    
    // $conn->query("INSERT INTO Song (song_title, track_number, album_id, song_file) 
    //      ('$song_title', '$track', '$album_id', '$song_file_name')");
 
    // INSERT into the song table with the inputted values
    
     // INSERT TO DATABASE
    $conn->query("INSERT INTO Song (song_title, track_number, album_id, song_file) 
                  VALUES ('$song_title', '$track', '$album_id', '$song_file_name')"); // inputted values

    echo "<script>alert('Track Ripped Successfully!'); window.location='list.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rip CD to Archive</title>
    <!-- montserrat font download -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
<nav><h2>🎵 CD Archive</h2><div class="nav-links"><a href="index.php">Home</a><a href="list.php">List</a><a href="add.php">Add</a></div></nav>
<main class="container">
    <div class="form-wrapper">
        <h1>Add to Archive</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="input-group">
                <label>Adding Mode</label>
                <!--select the option-->
                <select name="add_mode" id="addMode" class="select-style" onchange="toggleFields()">
                    <option value="new">New Album Archive</option>
                    <option value="existing">Add Track to Existing Album</option>
                </select>
                <!-- radio buttons so it looks better!!! (UPDATE! 2:17pm 4/5/26 IT DOESNT WORK NEVERMIND LMAO) -->
                <!-- <input type="radio" name="add_mode" value="new" id="modeNew" checked onchange="toggleFields()"><label for="modeNew">New Album Archive</label>
                <input type="radio" name="add_mode" value="existing" id="modeExisting" onchange="toggleFields()"><label for="modeExisting">Add Track to Existing Album</label> -->
            </div>

            <div id="newAlbumFields">
                <div class="input-group"><label>Artist Name</label><input type="text" name="artist_name"></div>
                <div class="input-group"><label>Album Title</label><input type="text" name="album_title"></div>
                <div class="input-group"><label>Year</label><input type="number" name="release_year"></div>
                <div class="input-group">
                  <label>Date Acquired</label>
                  <input type="date" name="date_acquired" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="input-group"><label>Condition</label>
                    <select name="condition_status" class="select-style">
                        <option value="Brand New">Brand New</option>
                        <option value="Used (Good Condition)">Used (Good Condition)</option>
                        <option value="Used (Poor Condition)">Used (Poor Condition)</option>
                        <option value="Unusable (Badly Scratched)">Unusable (Badly Scratched)</option>
                    </select>
                </div>
                <div class="input-group"><label>Album Cover (JPG/PNG)</label><input type="file" name="album_art" accept="image/*"></div>
            </div>

            <div id="existingAlbumFields" style="display:none;">
                <div class="input-group">
                    <label>Select Album</label>
                    <select name="existing_album_id" class="select-style">
                        <?php while($row = $existing_albums->fetch_assoc()): ?>
                            <option value="<?php echo $row['album_id']; ?>"><?php echo htmlspecialchars($row['album_title'] . ' - ' . $row['artist_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <hr style="margin:20px 0; border:0; border-top:1px solid #eee;">
            
            <div class="input-group"><label>Song Title</label><input type="text" name="song_title" required></div>
            <div class="input-group"><label>Track #</label><input type="number" name="track_number" required></div>
            
            <div class="input-group">
                <label>Upload Audio File (.mp3, .wav)</label>
                <input type="file" name="music_file" accept="audio/*" required>
            </div>
            
            <button type="submit" class="btn-submit">Rip & Save</button>
        </form>
    </div>
</main>
<footer>
  <p>Made by the Group 2 of BSIT-1A for Fundamentals of Database Systems :3</p>
</footer>

<script>
function toggleFields() {
    var mode = document.getElementById('addMode').value;
    document.getElementById('newAlbumFields').style.display = (mode === 'new') ? 'block' : 'none';
    document.getElementById('existingAlbumFields').style.display = (mode === 'existing') ? 'block' : 'none';
}
</script>
</body>
</html>