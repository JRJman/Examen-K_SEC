<?php
    session_start();
    require "php/functions.php";

    $title = "";
    $description = "";
    $errors = [];
    $errors["title"] = "";
    $errors["description"] = "";
    $errors["video"] = [];

    $done = "";

    if(isset($_SESSION["account"][0]) && isset($_SESSION["account"][1]) && checkCode($_SESSION["account"][0], $_SESSION["account"][1])){
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["title"]) && isset($_POST["description"])) {
            $title = $_POST["title"];
            $description = $_POST["description"];
            $video = $_FILES["video"];
            $uploadVideo = [];

            if(strlen($title) < 6 || strlen($title) > 50){
                $errors["title"] = "Titel moet uit 5 tot 50 characters bestaan.";
            }

            if(strlen($description) < 20|| strlen($description) > 200){
                $errors["description"] = "Omschrijving moet uit 20 tot 200 characters bestaan.";
            }

            if($video["name"] === ""){
                $errors["video"] = ["Video moet toegevoegd zijn"];
            } else {
                $uploadVideo = uploadVideo($video, true);
            }

            if(!empty($uploadVideo)){
                $errors["video"] = $uploadVideo;
            } else if($errors["title"] === "" && $errors["description"] === "") {
                createReportage($title, $description, $video);
                $title = "";
                $description = "";
                $done = "Report has been uploaded";
            }
        }
    } else {
        header("Location: login.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload page</title>
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
    <div class="wrapper">
        <?php include "templates/header.php" ?>

        <main>
            <form class="form" method="POST" enctype="multipart/form-data">
                <h2>Reportage aanmaken</h2>
                <div class="done"><?php echo $done ?></div>
                <div class="errors"><?php echo isset($errors["title"]) ? $errors["title"] : "" ?></div>
                <label>Titel</label>
                <input type="text" name="title" value="<?php echo $title ?>" required/>
                <div class="errors"><?php for ($i=0; $i < count($errors["video"]); $i++) { echo $errors["video"][$i]; if($i !==count($errors["video"])){echo "<br>";} } ?></div>
                <label>Video</label>
                <input type="file" name="video" required/>
                <div class="errors"><?php echo isset($errors["description"]) ? $errors["description"] : "" ?></div>
                <label>Beschrijving</label>
                <textarea name="description" rows="5" cols="50"><?php echo $description ?></textarea><br>
                <input type="submit" value="Reportage uploaden"/>
            </form>
        </main>

        <?php include "templates/footer.php" ?>
        <script src="js/javascript.js"></script>
    </div>
</body>
</html>