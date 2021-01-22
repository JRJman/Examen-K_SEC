<?php
    session_start();
    require "php/functions.php";

    $report = "";
    $title = "";
    $description = "";
    $errors = [];
    $errors["title"] = "";
    $errors["description"] = "";
    $errors["video"] = [];

    if(isset($_SESSION["account"][0]) && isset($_SESSION["account"][1]) && checkCode($_SESSION["account"][0], $_SESSION["account"][1])){
        if(isset($_GET["i"])){
            $con = dbConnect();
            $sql = "SELECT * FROM `reports` WHERE `id`=?";
            $prep = $con->prepare($sql);
            $prep->execute([$_GET["i"]]);
            $report = $prep->fetch();

            if(!isset($report["id"]) || $report["user_id"] !== $_SESSION["account"][0]){
                $sql = "SELECT * FROM `users` WHERE id=?";
                $prep = $con->prepare($sql);
                $prep->execute([$_SESSION["account"][0]]);
                $user = $prep->fetch();
        
                if(!$user["admin"]){
                    header("Location: index.php");
                }
            }

            $title = $report["title"];
            $description = $report["description"];
        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["title"]) && isset($_POST["description"])) {
        $title = $_POST["title"];
        $description = $_POST["description"];
        $video = $_FILES["video"]["name"] === "" ? false : $_FILES["video"];
        $uploadVideo = [];

        if(strlen($title) < 6 || strlen($title) > 50){
            $errors["title"] = "Titel moet uit 5 tot 50 characters bestaan.";
        }

        if(strlen($description) < 20|| strlen($description) > 200){
            $errors["description"] = "Omschrijving moet uit 20 tot 200 characters bestaan.";
        }

        if($video){
            $errors["video"] = checkVideo($video);
        }

        if($errors["title"] === "" && $errors["description"] === "" && $errors["video"] === []){
            updateReportage($title, $description, $video, $report["id"]);
            $sql = "SELECT * FROM `users` WHERE `id`=?";
            $prep = $con->prepare($sql);
            $prep->execute([$_SESSION["account"][0]]);
            $user = $prep->fetch();

            if($user["admin"]){
                header("Location: admin.php");
            }
            header("Location: profiel.php");
        }
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
            <form class="form" method="POST" enctype="multipart/form-data">
                <h2>Reportage aanpassen</h2>
                <div class="errors"><?php echo isset($errors["title"]) ? $errors["title"] : "" ?></div>
                <label>Titel</label>
                <input type="text" name="title" value="<?php echo $title ?>" required/>
                <div class="errors"><?php for ($i=0; $i < count($errors["video"]); $i++) { echo $errors["video"][$i]; if($i !==count($errors["video"])){echo "<br>";} } ?></div>
                <label>Video</label>
                <div>Voeg alleen een video toe wanneer je het wil aanpassen</div>
                <input type="file" name="video"/>
                <div class="errors"><?php echo isset($errors["description"]) ? $errors["description"] : "" ?></div>
                <label>Beschrijving</label>
                <textarea name="description" rows="5" cols="50"><?php echo $description ?></textarea><br>
                <input type="submit" value="Reportage aanpassen"/>
            </form>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>