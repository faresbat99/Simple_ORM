<?php
require '../models/User.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    //escape form sql injection
    // $email = mysqli_escape_string($conn, $_POST["email"]);
    $hash = sha1($_POST["password"]);
    // $hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user=new User();
    // $user->select("users","`email`='" . $_POST["email"] . "' and `password`='" . $hash."' ");
    // $user->LoginUsers($_POST["email"],$hash);
    // $query = "SELECT * FROM users where `email`='" . $email . "' and `password`='" . $hash . "' LIMIT 1";

    if ($row =$user->LoginUsers($_POST["email"],$hash)) {
        // if (password_verify($_POST['password'], $hash)) {
        $_SESSION["id"] = $row["id"];
        $_SESSION["name"] = $row["name"];
        header("Location: list.php");
    } else {
        $error = "Invalid Email or password";
    }
}
?>
<?php if (isset($error)) {
    echo $error;
} ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

</head>

<body>
    <h1>Login</h1>
    <form method="post">
        <table>
            <tr>
                <td> <label for="text">Email</label></td>
                <td> <input type="email" name="email" id="email" required value=<?= isset($_POST['email']) ? $_POST['email'] : '' ?>></td>
            </tr>
            <tr>
                <td> <label for="text">Password</label></td>
                <td><input type="password" name="password" id="password" required value=<?= isset($_POST['password']) ? $_POST['password'] : '' ?>></td>
            </tr>
        </table>
        <input type="submit" value="Login">
    </form>
</body>

</html>