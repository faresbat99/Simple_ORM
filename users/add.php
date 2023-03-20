<?php
session_start();
require '../models/User.php';
if ($_SESSION["id"] == null) {
    header("Location: login.php");
}
// check if the All input is set and return the error message in array
$error_fields = array();
error_reporting(E_ERROR);
if ($_SERVER['REQUEST_METHOD'] == "POST") { // لازم تتحفظ و خد بالك بوست كابيتال

    if (!(isset($_POST['name']) && !empty($_POST['name']))) {
        $error_fields[] = "name";
    }
    if (!(isset($_POST['email']) && filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL))) {
        $error_fields[] = "email";
    }
    if (!(isset($_POST['password']) && strlen($_POST['password']) > 5)) {
        $error_fields[] = "password";
    }
    
    if (!$error_fields) {
        $password = sha1($_POST['password']);
        $admin = $_POST['admin'] ? "1" : "0";

        //Uploading file 
        $upload_dir = "../uploads";
        $avatar = '';
        if ($_FILES['avatar']['name']) {

            if ($_FILES['avatar']['error'] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['avatar']["tmp_name"];
                $avatar = basename($_FILES['avatar']['name']);
                $ext = strtolower(pathinfo($avatar, PATHINFO_EXTENSION));
                if ($ext != "jpg" && $ext != "png" && $ext != "jpeg") {
                    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
                    return 0;
                }
                move_uploaded_file($tmp_name, "$upload_dir/" . $_POST['name'] . ".$avatar");
            } else {
                echo "file can't be upload !";
                exit;
            }
        }
        try {

            // execute and redirect
            $user = new User();
            $user->addUser(["name" => $_POST['name'], "email" => $_POST['email'], "password" => $password, "avatar" => $avatar, "admin" => $admin]);
            header("Location: list.php");
            exit;
        } catch (Exception $e) {
            echo "Duplicate the user email";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin :: Add User</title>
</head>

<body>
    <form method="post" enctype="multipart/form-data">
        <label for="text">Name</label>
        <input type="text" name="name" id="name" value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>"><?= in_array("name", $error_fields) ? "Enter  Your Name" : ""; ?><br>
        <label for="text">Email</label>
        <input type="email" name="email" id="email" value="<?= isset($_POST['email']) ? $_POST['email'] : '' ?>"><?= in_array("email", $error_fields) ? "Enter a Valid Email" : ""; ?><br>
        <label for="text">Password</label>
        <input type="password" name="password" id="password" value="<?= isset($_POST['password']) ? $_POST['password'] : '' ?>"><?= in_array("password", $error_fields) ? "Enter password more than 6 character" : ""; ?><br>
        <label for="text">Admin</label>
        <input type="checkbox" name="admin" id="admin" <?= isset($_POST['admin']) ? "checked" : "" ?>><br>
        <label for="text">Avatar</label>
        <input type="file" name="avatar" id="avatar">
        <input type="submit" name="submit" value="Add user">

    </form>
</body>

</html>