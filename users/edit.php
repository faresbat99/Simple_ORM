<?php
require '../models/User.php';
session_start();
if ($_SESSION["id"]==null) {
    header("Location: login.php");
}
$error_fields = array();
//select the user
$user = new User();
// edit.php?id=1 =>$_GET['id']علشان تاخد اللي موجود فوق بتستعمل $_GET
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$row = $user->getUser($id);
//validation on data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!(isset($_POST['name']) && !empty($_POST['name']))) {
        $error_fields[] = "name";
    }
    if (!(isset($_POST['email']) && filter_input(INPUT_POST, "email", FILTER_VALIDATE_EMAIL))) { // don't forget email not $_Post["email"]
        $error_fields[] = "email";
    }

    // if no errors 
    if (!$error_fields) {
    
        $id = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT); // _GET['id']
        $password = (!empty($_POST['password'])) ? sha1($_POST['password']) : $row['password'];
        $admin = isset($_POST['admin']) ? "1" : "0";
        // update file
        $upload_dir = "../uploads";
        $avatar = '';
        if ($_FILES["avatar"]["error"] == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES["avatar"]["tmp_name"];
            $avatar = basename($_FILES["avatar"]["name"]);
            if (is_dir("$upload_dir/".$_POST['name'].".$avatar")) {
                unlink("$upload_dir/".$_POST['name'].".$avatar");
            }
            move_uploaded_file($tmp_name, "$upload_dir/".$_POST['name'].".$avatar");
        } else {
            echo "cannot upload file";
        }
        //Update the data  
        $user->updateUser(["name" => $_POST['name'], "email" => $_POST['email'], "password" => $password, "avatar" => $avatar, "admin" => $admin],$id);
        header("Location: list.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin :: Edit User</title>
</head>

<body>
    <h1>Admin :: Edit User</h1>
    <form method="post" enctype="multipart/form-data">
        <label for="text">id</label>
        <input type="text" name="id" id="id" disabled value="<?= isset($row['id']) ? $row['id'] : '' ?>"><br>
        <label for="text">Name</label>
        <input type="text" name="name" id="name" value="<?= isset($row['id']) ? $row['name'] : '' ?>"><?= in_array("name", $error_fields) ? "Enter  Your Name" : ""; ?><br>
        <label for="text">Email</label>
        <input type="email" name="email" id="email" value="<?= isset($row['email']) ? $row['email'] : '' ?>"><?= in_array("email", $error_fields) ? "Enter a Valid Email" : ""; ?><br>
        <label for="text">Password</label>
        <input type="password" name="password" id="password"><?= in_array("password", $error_fields) ? "Enter password more than 6 character" : ""; ?><br>
        <label for="text">Admin</label>
        <input type="checkbox" name="admin" id="admin" <?= $row['admin'] ? "checked" : "" ?>><br>
        <label for="text">Avatar</label>
        <input type="file" name="avatar" id="avatar">
        <input type="submit" name="submit" value="Edit User">
    </form>
</body>

</html>