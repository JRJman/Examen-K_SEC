<?php
    session_start();
    require "php/functions.php";

    $search = isset($_GET["s"]) ? $_GET["s"] : "";

    $con = dbConnect();
    $sql = "SELECT * FROM `reports` WHERE `title` LIKE ? OR `description` LIKE ? ORDER BY views DESC";
    $prep = $con->prepare($sql);
    $prep->execute(["%$search%", "%$search%"]);
    $reports = $prep->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <div class="main">
                <form class="search" action="search.php" method="GET">
                    <label>Zoeken</label>
                    <input type="text" name="s" placeholder="Zoeken..." value="<?php echo $search ?>">
                    <input type="submit" value="Zoeken">
                </form>
                <div class="list">
                    <h2>Resultaten</h2>
                    <?php
                        foreach ($reports as $report) {
                            echo "<div class='item'>";
                            echo "<div class='title'>" . $report["title"] . "</div>";
                            echo "<div class='views'>Aantal keer bekeken: " . $report["views"] . "</div>";
                            echo "<div><a href='report.php?i=" . $report["id"]  . "'>Bekijk report</a></div>";
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