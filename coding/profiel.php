<?php
    session_start();
    require "php/functions.php";
    $account = "";

    if(isset($_SESSION["account"][0]) && isset($_SESSION["account"][1]) && checkCode($_SESSION["account"][0], $_SESSION["account"][1])){
        $con = dbConnect();
        $sql = "SELECT * FROM `users` WHERE `id`=?";
        $prep = $con->prepare($sql);
        $prep->execute([$_SESSION["account"][0]]);
        $account = $prep->fetch();
    } else {
        header("Location: index.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profiel page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <div class="profiel">
                <h1><?php echo $account["username"] ?></h1>
                <h2>Contactgegevens</h2>
                <div class="contactgegevens">
                    <div>Email: <?php echo $account["email"];?></div>
                </div>
                <h2>Persoonlijke reports</h2>
                <div class="personal-reports">
                    <?php
                        $sql = "SELECT * FROM `reports` WHERE `user_id`=?";
                        $prep = $con->prepare($sql);
                        $prep->execute([$account["id"]]);
                        $reports = $prep->fetchAll();

                        foreach ($reports as $report) {
                            echo "<div class='item'>";
                            echo "<div>" . $report["title"] . "</div>";
                            echo "<div><a href='report.php?i=" . $report["id"] . "'>Bekijk report</a></div>";
                            echo "<div><a href='update-report.php?i=" . $report["id"] . "'>Update report</a></div>";
                            echo "<div><a href='delete-report.php?i=" . $report["id"] . "'>Delete report</a></div>";
                            echo "</div>";
                        }
                    ?>
                </div>
            </div>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>