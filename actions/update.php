<?php
include "../db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $album_id = (int)$_POST['update_album_id'];
    $album_title = $conn->real_escape_string($_POST['input_album_title']);
    $release_year = (int)$_POST['input_release_year'];
    $condition_status = $conn->real_escape_string($_POST['input_condition_status']);
    $date_acquired = $conn->real_escape_string($_POST['input_date_acquired']);

    // Update album details
    $update_query = "UPDATE cd_album SET 
                        album_title = '$album_title', 
                        release_year = $release_year, 
                        condition_status = '$condition_status', 
                        date_acquired = '$date_acquired' 
                    WHERE album_id = $album_id";

    if ($conn->query($update_query)) {
        header("Location: ../list.php?updated=1");
        exit;
    } else {
        die("Error editing! " . $conn->error);
    }
} else {
    die("error!!!");
}

header("Location: ../list.php?deleted=1");
exit;
?>

// real_escape_string is so cool it prevents spaces from being misunderstood by sql
