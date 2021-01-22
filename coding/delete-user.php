<?php
    session_start();
    require "php/functions.php";

    if(
        isset($_GET["i"]) && 
        isset($_SESSION["account"][0]) && 
        isset($_SESSION["account"][1]) && 
        checkCode($_SESSION["account"][0], $_SESSION["account"][1])){
            
        $check = false;

        $con = dbConnect();
        $sql = "SELECT * FROM `users` WHERE id=?";
        $prep = $con->prepare($sql);
        $prep->execute([$_SESSION["account"][0]]);
        $user = $prep->fetch();

        if($user["admin"]){
            $check = true;
        }

        if($check){
            $sql = "DELETE FROM `users`
                WHERE `id` = ?";

            $prep = $con->prepare($sql);
            $prep->execute([$_GET["i"]]);

            $sql = "DELETE FROM `reports`
                WHERE `user_id` = ?";

            $prep = $con->prepare($sql);
            $prep->execute([$_GET["i"]]);
            header("Location: admin.php");
        } else {
            header("Location: index.php");
        }
    } else {
        header("Location: index.php");
    }
?>
