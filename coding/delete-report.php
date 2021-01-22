<?php
    session_start();
    require "php/functions.php";

    if(
        isset($_GET["i"]) && 
        isset($_SESSION["account"][0]) && 
        isset($_SESSION["account"][1]) && 
        checkCode($_SESSION["account"][0], $_SESSION["account"][1])){
            
        $check = 0;

        $con = dbConnect();
        $sql = "SELECT * FROM `reports` WHERE id=?";
        $prep = $con->prepare($sql);
        $prep->execute([$_GET["i"]]);
        $report = $prep->fetch();

        if($report["user_id"] === $_SESSION["account"][0]){
            $check = 1;
        }
        
        $sql = "SELECT * FROM `users` WHERE id=?";
        $prep = $con->prepare($sql);
        $prep->execute([$_SESSION["account"][0]]);
        $user = $prep->fetch();

        if($user["admin"]){
            $check = 2;
        }

        if($check !== 0){
            $sql = "DELETE FROM `reports`
                WHERE `id` = ?";

            $prep = $con->prepare($sql);
            $prep->execute([$_GET["i"]]);
        } else {
            header("Location: index.php");
        }

        if($check === 1){
            header("Location: profiel.php");
        } else if($check === 2) {
            header("Location: admin.php");
        }

    } else {
        header("Location: index.php");
    }
?>
