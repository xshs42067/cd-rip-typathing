<?php
include "../db.php";

if (isset($_POST['delete_song_id'])) {
    // delete song file from disk if it exists, then delete the song record from DB
    $song_id = (int)$_POST['delete_song_id'];
    $song_res = $conn->query("SELECT song_file FROM Song WHERE song_id = $song_id");
    if ($song_res && $song_res->num_rows > 0) {
        $song_data = $song_res->fetch_assoc();
        $file_path = dirname(__DIR__) . "/music/" . $song_data['song_file'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    $conn->query("DELETE FROM Song WHERE song_id = $song_id");
}
// delete album
if (isset($_POST['delete_album_id'])) {
    $album_id = (int)$_POST['delete_album_id'];
    // update: oopsies forgot to add artist id
    $album_res = $conn->query("SELECT album_art, artist_id FROM cd_album WHERE album_id = $album_id");
    $album_data = $album_res->fetch_assoc();
    $artist_id = $album_data['artist_id'];
    if ($album_res && $album_res->num_rows > 0) {
        $album_data = $album_res->fetch_assoc();
        if ($album_data['album_art'] && $album_data['album_art'] !== "noimage.png") {
            $art_path = dirname(__DIR__) . "/uploads/" . $album_data['album_art'];
            if (file_exists($art_path)) {
                unlink($art_path);
            }
        }
    }
    $conn->query("DELETE FROM cd_album WHERE album_id = $album_id");

    // artist deletion kung wala na it songs nga ga exist, *sighs* added 4/23/26
    $isitZero = $conn->query("SELECT * FROM cd_album WHERE artist_id = $artist_id");
    if ($isitZero->num_rows == 0) {
        $conn->query("DELETE FROM artist WHERE artist_id = $artist_id");
    }
}
header("Location: ../add.php?deleted=1");
exit;
?>