<?php
session_start();
require '../models/User.php';
if ($_SESSION["id"]==null) {
    header("Location: login.php");
}
$user = new User();
// //receiving id 
$id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
$user->deleteUser($id);

// $query = "DELETE  FROM `users` WHERE `id`=$id";
// if (mysqli_query($conn, $query)) {
//     // we want to delete the photo that is store in db 
$name = $_GET["name"];
$avatar = $_GET["avatar"];
unlink("../uploads/" . "$name" . "." . "$avatar");
header("Location: list.php");
//     exit;
// } else
//     echo mysqli_error($conn);
// mysqli_close($conn);
