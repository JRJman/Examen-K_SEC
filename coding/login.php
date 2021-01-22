<?php
    session_start();
    require "php/functions.php";

    $username = "";
    $error = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["username"]) && isset($_POST["password"])){
            $username = $_POST["username"];
            $password = $_POST["password"];

            $loginCheck = login($username, $password);

            if($loginCheck){
                header("Location: index.php");
            } else {
                $error = "Gebruikersnaam of Wachtwoord is verkeerd";
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <form class="form" method="POST">
                <h2>Login</h2>
                <div class="errors"><?php echo $error ?></div>
                <label>Gebruikersnaam</label>
                <input type="text" name="username" value="<?php echo $username ?>" required/>
                <label>Wachtwoord</label>
                <input type="password" name="password" required/>
                <input type="submit" value="Login"/>
                <a href="forgot-password.php">Wachtwoord vergeten</a>
            </form>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>