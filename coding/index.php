<?php
    session_start();
    require "php/functions.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <div class="main">
                <form class="search" action="search.php" method="GET">
                    <label>Zoeken</label>
                    <input type="text" name="s" placeholder="Zoeken...">
                    <input type="submit" value="Zoeken">
                </form>
                <div class="list">
                    <h2>5 meest bekeken videos</h2>
                    <?php
                        $con = dbConnect();
                        $sql = "SELECT * FROM `reports` ORDER BY views DESC";
                        $prep = $con->prepare($sql);
                        $prep->execute();
                        $reports = $prep->fetchAll();

                        for ($i=0; $i < 5; $i++) {
                            if(isset($reports[$i])){
                                echo "<div class='item'>";
                                echo "<div class='title'>" . $reports[$i]["title"] . "</div>";
                                echo "<div class='views'>Aantal keer bekeken: " . $reports[$i]["views"] . "</div>";
                                echo "<div><a href='report.php?i=" . $reports[$i]["id"]  . "'>Bekijk report</a></div>";
                                echo "</div>";
                            }
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