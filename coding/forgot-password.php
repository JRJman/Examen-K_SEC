<?php
    session_start();
    require "php/functions.php";

    $email = "";
    $error = "";
    $done = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["email"])) {
        $email = $_POST["email"];
        $errorCheck = sendPasswordForgotEmail($email);
        
        if($errorCheck){
            $done = "De email om je wachtwoord te versturen is opgestuurd";
        } else {
            $error = "Email is fout.";
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord vergeten page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <form class="form" method="POST">
                <h2>Voer jouw email in</h2>
                <div class="errors"><?php echo $error ?></div>
                <div class="done"><?php echo $done ?></div>
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email ?>" required/>
                <input type="submit" value="Opsturen"/>
            </form>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>