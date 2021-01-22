<?php
    session_start();
    require "php/functions.php";

    $password1 = "";
    $password2 = "";
    $errors = [];
    $errors["password1"] = "";
    $errors["password2"] = "";

    if(isset($_GET["i"]) && isset($_GET["c"])){
        $checker = checkCode($_GET["i"], $_GET["c"]);
        if(!$checker){
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["password1"]) && isset($_POST["password2"])) {
        $password1 = $_POST["password1"];
        $password2 = $_POST["password2"];

        if(strlen($password1) < 6){
            $errors["password1"] = "Wachtwoord moet in ieder geval uit 6 tekens bestaan";
        }

        if($password1 !== $password2){
            $errors["password2"] = "wachtwoorden zijn niet hetzelfde";
        }

        if($errors["password1"] === "" && $errors["password2"] === ""){
            changePassword($_GET["i"], $password1);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password change page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <form class="form" method="POST">
                <h2>Wachtwoord aanpassen</h2>
                <div class="errors"><?php echo $errors["password1"] ?></div>
                <label>Nieuw wachtwoord</label>
                <input type="password" name="password1" required/>
                <div class="errors"><?php echo $errors["password2"] ?></div>
                <label>Herhaal wachtwoord</label>
                <input type="password" name="password2" required/>
                <input type="submit" value="Wachtwoord aanpassen"/>
            </form>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>