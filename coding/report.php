<?php
    session_start();
    require "php/functions.php";

    $report = "";

    if(isset($_GET["i"])){
        $con = dbConnect();
        $sql = "SELECT * FROM `reports` WHERE `id`=?";
        $prep = $con->prepare($sql);
        $prep->execute([$_GET["i"]]);
        $report = $prep->fetch();

        if(!isset($report["id"])){
            header("Location: index.php");
        }

        $sql = "UPDATE `reports`
                SET `views`=?
                WHERE `id`=?";
        $prep = $con->prepare($sql);
        $prep->execute([($report["views"] + 1),$_GET["i"]]);
    } else {
        header("Location: index.php");
    }
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
                <h1><?php echo $report["title"]?></h1>
                <div>
                <video width="100%" controls>
                    <source src="<?php echo $report["video"] ?>" type="video/mp4">
                    <source src="<?php echo $report["video"] ?>" type="video/ogg">
                    <source src="<?php echo $report["video"] ?>" type="video/webm">
                    Your browser does not support the video tag.
                </video>
                </div>
                <div>Amount of views: <?php echo ($report["views"] + 1) ?></div>
                <h2>Description</h2>
                <div><?php echo $report["description"] ?></div>
            </div>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>