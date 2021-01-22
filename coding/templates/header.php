<?php
    $login = false;
    $admin = false;

    if(isset($_SESSION["account"][0]) && isset($_SESSION["account"][1]) && checkCode($_SESSION["account"][0], $_SESSION["account"][1])){
        $login = true;
        
        $con = dbConnect();
        $sql = "SELECT * FROM `users` WHERE `id`=?";
        $prep = $con->prepare($sql);
        $prep->execute([$_SESSION["account"][0]]);
        $account = $prep->fetch();

        if($account["admin"]){
            $admin = true;
        }
    }
?>

<header>
    <nav>
        <a class="hover" href='index.php'>Home</a>
        <a class="hover" href='search.php'>Search</a>
        <?php
            if($login){
                echo "<a class='hover' href='upload.php'>Report Aanmaken</a>";
                if($admin){
                    echo "<a class='hover' href='admin.php'>Admin</a>";
                } else {
                    echo "<a class='hover' href='profiel.php'>Profiel</a>";
                }
                echo "<a class='hover' href='logout.php'>Uitloggen</a>";
            } else {
                echo "<a style='color:#ccffff;cursor:default;' >t</a>";
                echo "<a class='hover' href='signup.php'>Account Aanmaken</a>";
                echo "<a class='hover' href='login.php'>Login</a>";
            }
        ?>
    </nav>
</header>