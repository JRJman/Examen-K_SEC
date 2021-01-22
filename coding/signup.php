<?php
    session_start();
    require "php/functions.php";

    $username = "";
    $email = "";
    $errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if(isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["email"])){
            $username = $_POST["username"];
            $password = $_POST["password"];
            $email = $_POST["email"];

            $con = dbConnect();

            $sql = "SELECT * FROM users WHERE username=?";
            $prep = $con->prepare($sql);
            $prep->execute([$username]);
            $sameUsernames = $prep->fetchAll();

            $sql = "SELECT * FROM users WHERE email=?";
            $prep = $con->prepare($sql);
            $prep->execute([$email]);
            $sameEmail = $prep->fetchAll();


            if(strlen($username) < 6){
                $errors["username"] = "Gebruikersnaam moet in ieder geval uit 6 tekens bestaan";
            } else if(count($sameUsernames)){
                $errors["username"] = "Gebruikersnaam is al in gebruik";
            }

            if(strlen($password) < 6){
                $errors["password"] = "Wachtwoord moet in ieder geval uit 6 tekens bestaan";   
            }

            if(count($sameEmail)){
                $errors["email"] = "Email is al in gebruik";
            }

            if($errors === []){
                createAccount($username, $password, $email);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <form class="form" method="POST">
                <h2>Account Aanmaken</h2>
                <div class="errors"><?php echo isset($errors["username"]) ? $errors["username"] : "" ?></div>
                <label>Gebruikersnaam</label>
                <input type="text" name="username" value="<?php echo $username ?>" required/>
                <div class="errors"><?php echo isset($errors["password"]) ? $errors["password"] : "" ?></div>
                <label>Wachtwoord</label>
                <input type="password" name="password" required/>
                <div class="errors"><?php echo isset($errors["email"]) ? $errors["email"] : "" ?></div>
                <label>Email</label>
                <input type="email" name="email" value="<?php echo $email ?>" required/>
                <input type="submit" value="Account Aanmaken"/>
            </form>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>