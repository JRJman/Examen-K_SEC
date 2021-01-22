<?php
    session_start();
    require "php/functions.php";
    
    $answer = "";

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET["c"]) && isset($_GET["i"])) {
        $id = $_GET["i"];
        $code = $_GET["c"];
        $checkCode = activateAccount($id, $code);

        if($checkCode){
            $answer = "Uw account is nu geactiveerd. <br>Je kunt nu inloggen op uw account";
        } else {
            $answer = "Deze link is ongeldig.";
        }
    } else {
        header("Location: index.php");
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Activation page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <div class="answer"><?php echo $answer; ?></div>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>