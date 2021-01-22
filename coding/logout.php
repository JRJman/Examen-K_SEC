<?php
    session_start();

    $_SESSION["account"] = [];

    header("Location: login.php");
?>