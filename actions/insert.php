<?php
// insert!
include "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $mode = $_POST['mode'];

    // deletion mode
    if ($mode === 'delete') {
        $song_id = (int)$_POST['delete_song_id'];
        $song_res = $conn->query("SELECT song_file FROM song WHERE song_id = $song_id");
        if ($song_res && $song_res->num_rows > 0) {
            $song_data = $song_res->fetch_assoc();
            $file_path = dirname(__DIR__) . "/music/" . $song_data['song_file'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
        $conn->query("DELETE FROM song WHERE song_id = $song_id");
        header("Location: ../list.php");
        exit;
    }

    // uhh??
    $song_title = $conn->real_escape_string($_POST['song_title']);
    $track_number = (int)$_POST['track_number'];

    // Add to existing album or create new album + artist?
    if ($mode === 'existing') {
        $album_id = (int)$_POST['existing_album_id'];
    } else {
        $artist_name = $conn->real_escape_string($_POST['artist_name']);
        $album_title = $conn->real_escape_string($_POST['album_title']);
        $release_year = (int)$_POST['release_year'];
        $condition_status = $conn->real_escape_string($_POST['condition_status']);
        $date_acquired = $conn->real_escape_string($_POST['date_acquired']);

        // checks if artist already exists, if not create new artist record! 
        $artist_res = $conn->query("SELECT artist_id FROM artist WHERE artist_name = '$artist_name'");
        if ($artist_res && $artist_res->num_rows > 0) {
            $artist_id = $artist_res->fetch_assoc()['artist_id'];
        } else {
            $conn->query("INSERT INTO artist (artist_name) VALUES ('$artist_name')");
            $artist_id = $conn->insert_id;
        }

        // upload album art and get the filename to save in DB (otherwise use default placeholder "noimage.png" default value)
        $art_path = "noimage.png";
        // album art uploading
        if (!empty($_FILES['album_art']['name'])) {
            $upload_dir = dirname(__DIR__) . "/uploads";
            if (!file_exists($upload_dir)) {
                // wait, im having permission issues with this part, NOTE: add 0777 and "true" mkdir($upload_dir, 0777, true) if it doesnt work, remember that time you need to do chmod in terminal just to run debian files? -hans 2k26
                mkdir($upload_dir);
            }
            // the random number is in UNIX time when it was added, to prevent something like filename overrides and stuff -hans 2k26
            $target_art = $upload_dir . "/" . time() . "_" . basename($_FILES['album_art']['name']);
            if (move_uploaded_file($_FILES['album_art']['tmp_name'], $target_art)) {
                $art_path = basename($target_art);
            }
        }

        // insert new album record and get its ID for the song foreign key,
        $conn->query("INSERT INTO cd_album (album_title, release_year, condition_status, album_art, artist_id, date_acquired) VALUES ('$album_title', '$release_year', '$condition_status', '$art_path', '$artist_id', '$date_acquired')");
        $album_id = $conn->insert_id;
    }

    // upload song file and get filename for DB
    $song_file_name = "";
    if (!empty($_FILES['music_file']['name'])) {
        $music_dir = dirname(__DIR__) . "/music";
        if (!file_exists($music_dir)) {
            mkdir($music_dir, 0777, true);
        }
        $song_file_name = time() . "_" . basename($_FILES['music_file']['name']);
        $target_music = $music_dir . "/" . $song_file_name;
        move_uploaded_file($_FILES['music_file']['tmp_name'], $target_music);
    }

    $conn->query("INSERT INTO song (song_title, track_number, album_id, song_file) VALUES ('$song_title', '$track_number', '$album_id', '$song_file_name')");
    header("Location: ../list.php");
    exit;
}
?>