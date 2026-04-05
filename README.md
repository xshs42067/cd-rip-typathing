To setup the website, install XAMPP on your pc then download all the files and paste on xampp/htdocs/cd_system directory.
Before visiting the website, the database should be initialized.

Run Apache and PHPMyAdmin and go to localhost/cd_system/index.php on your browser.
Create a database named "cd_system". then paste this SQL schema on the SQL tab of the database created.
```
DROP DATABASE IF EXISTS cd_system;
CREATE DATABASE cd_system;
USE cd_system;

CREATE TABLE Artist (
    artist_id INT AUTO_INCREMENT PRIMARY KEY,
    artist_name VARCHAR(255) NOT NULL UNIQUE
);

CREATE TABLE CD_Album (
    album_id INT AUTO_INCREMENT PRIMARY KEY,
    album_title VARCHAR(255) NOT NULL,
    release_year INT,
    condition_status VARCHAR(100),
    album_art VARCHAR(255) DEFAULT 'noimage.png',
    artist_id INT,
    FOREIGN KEY (artist_id) REFERENCES Artist(artist_id) ON DELETE CASCADE
);

CREATE TABLE Song (
    song_id INT AUTO_INCREMENT PRIMARY KEY,
    song_title VARCHAR(255) NOT NULL,
    track_number INT,
    album_id INT,
    FOREIGN KEY (album_id) REFERENCES CD_Album(album_id) ON DELETE CASCADE
);
```
You can now use the website.


