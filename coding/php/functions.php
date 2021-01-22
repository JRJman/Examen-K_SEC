<?php
  function dbConnect() {
    $config = require __DIR__ . '/db_config.php';

    try {
    $dsn = "mysql:host=" . $config['db_host'] . ';dbname=' . $config['db_name'];
    $database = new PDO($dsn, $config['db_user'], $config['db_pass']);

    $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $database->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $database;

    } catch (PDOException $fout) {
        echo "Database connectie fout: " . $fout->getMessage();
        exit;
    }

  }

  function createAccount($username, $password, $email) {
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $accountCode = createAccountCode();
    $con = dbConnect();
    $sql = "INSERT INTO `users` (`username`,`password`,`email`,`code`,`active`,`admin`) 
            VALUES (?,?,?,?,?,?)";
    $prep = $con->prepare($sql);
    $prep->execute([$username,$hashedPassword,$email,$accountCode,false,false]);
    $id = $con->lastInsertId();

    sendAccountActivationEmail($id);
  }

  function sendAccountActivationEmail($id) {
    $con = dbConnect();
    $sql = "SELECT * FROM `users` WHERE `id`=?";
    $prep = $con->prepare($sql);
    $prep->execute([$id]);
    $account = $prep->fetch();

    $to      = $account["email"];
    $subject = 'Account Activatie';
    $message = 'Ga naar deze link toe om het account te activeren.\r\n' . 
                $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . "/account-activation.php?i=" . $id . "&c=" . password_hash($account["code"], PASSWORD_BCRYPT);
    $headers = 'From: joepnep@gmail.com' . "\r\n" .
               'Reply-To: joepnep@gmail.com' . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    // var_dump($message);

    mail($to, $subject, $message, $headers);
  }

  function activateAccount($id, $code) {
    $returnValue = checkCode($id, $code);

    if($returnValue){
      $newCode = createAccountCode();
      $con = dbConnect();
      $sql = "UPDATE `users`
              SET `code` = ?, `active` = ?
              WHERE `id` = ?";
      $prep = $con->prepare($sql);
      $prep->execute([$newCode, true, $id]);
    }

    return $returnValue;
  }

  function login($username, $password) {
    $returnValue = false;
    
    $con = dbConnect();
    $sql = "SELECT * FROM `users` WHERE `username`=? AND `active`=?";
    $prep = $con->prepare($sql);
    $prep->execute([$username, true]);
    $accounts = $prep->fetchAll();

    foreach ($accounts as $account) {
      if(password_verify($password, $account["password"])){
        $returnValue = true;
        changeCode($account["id"]);
        $sql = "SELECT * FROM `users` WHERE `id`=?";
        $prep = $con->prepare($sql);
        $prep->execute([$account["id"]]);
        $newAccount = $prep->fetch();

        $_SESSION["account"] = [$newAccount["id"], password_hash($newAccount["code"], PASSWORD_BCRYPT)];
      }
    }

    return $returnValue;
  }

  function sendPasswordForgotEmail($email) {
    $returnValue = false;
    
    $con = dbConnect();
    $sql = "SELECT * FROM `users` WHERE `email`=? AND `active`=?";
    $prep = $con->prepare($sql);
    $prep->execute([$email, true]);
    $account = $prep->fetch();

    if(!empty($account)){
      sendWachtwoordAanpassenmail($account); 
      $returnValue = true;
    }

    return $returnValue;
  }

  function sendWachtwoordAanpassenmail($account) {
    $to      = $account["email"];
    $subject = 'WQachtwoord aanassen';
    $message =  'Ga naar deze link toe om je wachtwoord aan te passen. \r\n' . 
                $_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']) . "/change-password.php?i=" . $account["id"] . "&c=" . password_hash($account["code"], PASSWORD_BCRYPT);
    $headers =  'From: joepnep@gmail.com' . "\r\n" .
                'Reply-To: joepnep@gmail.com' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
  
    // var_dump($message);
  
    mail($to, $subject, $message, $headers);
  }

  function changePassword($id, $password) {
    changeCode($id);
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    $con = dbConnect();
    $sql = "UPDATE `users`
            SET `password` = ?
            WHERE `id` = ?";
    $prep = $con->prepare($sql);
    $prep->execute([$hashedPassword, $id]);
  }

  function checkCode($id, $code) {
    $returnValue = false;

    $con = dbConnect();
    $sql = "SELECT * FROM `users` WHERE `id`=?";
    $prep = $con->prepare($sql);
    $prep->execute([$id]);
    $account = $prep->fetch();

    if(password_verify($account["code"], $code)){
      $returnValue = true;
    }

    return $returnValue;
  }

  function changeCode($id) {
    $newCode = createAccountCode();
    $con = dbConnect();
    $sql = "UPDATE `users`
            SET `code` = ?
            WHERE `id` = ?";
    $prep = $con->prepare($sql);
    $prep->execute([$newCode, $id]);
  }

  function createAccountCode() {
    $returnValue = "";
    for ($i=0; $i < 5; $i++) { 
      $returnValue .= chr(rand(65,90));      
    }
    return $returnValue;
  }

  function uploadVideo($video, $upload) {
    $con = dbConnect();
    $sql = "SELECT * FROM `reports` ORDER BY `id` DESC";
    $prep = $con->query($sql);
    $number = $prep->fetchAll()[0]["id"] + 1;

    $inipath = php_ini_loaded_file();

    $errors = array();
    $video_name = $video['name'];
    $video_size = $video['size'];
    $video_tmp = $video['tmp_name'];
    $video_type = $video['type'];
    $tmp = explode('.', $video_name);
    $video_ext = strtolower(end($tmp));
    $video_name = "video" . $number . "." . strtolower(end($tmp));
            
    $expensions= ["mp4", "webm", "oog"];
            
    if(in_array($video_ext,$expensions)=== false){
      $errors[]="Video moet een WEBM, MP4 of OOG video.";
    }
    
    if(empty($errors) == true && $upload) {
      move_uploaded_file($video_tmp, "videos/" . $video_name);
    }

    return $errors;
  }

  function createReportage($title, $description, $video) {
    $con = dbConnect();
    $sql = "SELECT * FROM `reports` ORDER BY `id` DESC";
    $prep = $con->query($sql);
    $number = $prep->fetchAll()[0]["id"] + 1;

    $video_name = $video['name'];
    $tmp = explode('.', $video_name);
    $video_name = "video" . $number . "." . strtolower(end($tmp));

    $sql = "INSERT INTO `reports` (`title`,`description`,`video`,`user_id`) 
            VALUES (?,?,?,?)";
    $prep = $con->prepare($sql);
    $prep->execute([$title, $description,("videos/" . $video_name),$_SESSION["account"][0]]);
  }

  function updateReportage($title, $description, $video, $id) {
    $con = dbConnect();
    $video_name = "";

    if(!$video){
      $sql = "SELECT * FROM `reports` WHERE `id`=?";
      $prep = $con->prepare($sql);
      $prep->execute([$id]);
      $video_name = $prep->fetch()["video"];
    } else {
      $video_name = $video['name'];
      $video_tmp = $video['tmp_name'];
      $tmp = explode('.', $video_name);
      $video_name = "video" . $id . "." . strtolower(end($tmp));
      move_uploaded_file($video_tmp, "videos/" . $video_name);

      $video_name = "videos/video" . $id . "." . strtolower(end($tmp));
    }

    $sql = "UPDATE `reports` 
            SET `title`=?, `description`=?, `video`=?
            WHERE `id`=?";
    $prep = $con->prepare($sql);
    $prep->execute([$title, $description, $video_name, $id]);
  }

  function checkVideo($video) {
    if(!$video){
      $video_name = $video['name'];
      $video_tmp = $video['tmp_name'];
      $tmp = explode('.', $video_name);
      $video_ext = strtolower(end($tmp));
      $video_name = "video" . $number . "." . strtolower(end($tmp));
              
      $expensions= ["mp4", "webm", "oog"];
              
      if(in_array($video_ext,$expensions)=== false){
        return ["Video moet een WEBM, MP4 of OOG video."];
      }
    }

    return [];
  }
?>
