<?php

$host = "localhost";
$user = "root";
$password = "";
$db = "cd_system";

$db_name = "cd_system";

// fixed. removed db name bcs itll execute first before checking if a database exists that leads to that error nga nakita ko sa website
$conn = new mysqli($host, $user, $password);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// initialization
$conn->query("CREATE DATABASE IF NOT EXISTS $db_name");
$conn->select_db($db_name);

$conn->query("CREATE TABLE IF NOT EXISTS artist (artist_id INT AUTO_INCREMENT PRIMARY KEY, artist_name VARCHAR(255) NOT NULL UNIQUE)");

// album
$conn->query("CREATE TABLE IF NOT EXISTS cd_album (
    album_id INT AUTO_INCREMENT PRIMARY KEY,
    album_title VARCHAR(255) NOT NULL,
    release_year INT,
    condition_status VARCHAR(100),
    album_art VARCHAR(255) DEFAULT 'noimage.png',
    artist_id INT,
    date_acquired DATE, -- nalipatan, were good
    FOREIGN KEY (artist_id) REFERENCES artist(artist_id) ON DELETE CASCADE
)");

// song
$conn->query("CREATE TABLE IF NOT EXISTS song (
    song_id INT AUTO_INCREMENT PRIMARY KEY,
    song_title VARCHAR(255),
    track_number INT,
    album_id INT NOT NULL,
    song_file VARCHAR(255),
    FOREIGN KEY (album_id) REFERENCES cd_album(album_id) ON DELETE CASCADE
)");

?>