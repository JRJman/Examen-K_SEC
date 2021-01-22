<?php
    include 'functions.php';
    $con = dbConnect();

    if(isset($_POST['productInsert'])){
        $name = $_POST['productInsert']['name'];
        $description = $_POST['productInsert']['description'];
        $type = $_POST['productInsert']['type'];
        $archived = isset($_POST['productInsert']['archived']);

        $sql = "INSERT INTO product (name,description,type,`is_archived`) 
                VALUES (?,?,?,?)";
        $prep = $con->prepare($sql);
        $prep->execute([$name,$description,$type,$archived]);
    }

    if(isset($_POST['productUpdate'])){
        $id = $_POST['productUpdate']['id'];
        $name = $_POST['productUpdate']['name'];
        $description = $_POST['productUpdate']['description'];
        $type = $_POST['productUpdate']['type'];
        $archived = isset($_POST['productUpdate']['archived']);

        $sql = "UPDATE product
                SET name = ?, description = ?, type = ?, `is_archived` = ?
                WHERE id = ?";
        $prep = $con->prepare($sql);
        $prep->execute([$name,$description,$type,$archived,$id]);
    }

    if(isset($_POST['productDelete'])){
        $id = $_POST['productDelete']['id'];

        $sql = "DELETE FROM product
                WHERE id = ?";

        $prep = $con->prepare($sql);
        $prep->execute([$id]);
    }


    $sql = "SELECT * 
            FROM product 
            WHERE id=?";
    $prep = $con->prepare($sql);
    $prep->execute([1]);
    $selectContent = $prep->fetchAll();


    $sql = "SELECT product.*
            FROM customer_product
            JOIN customer ON customer_product.customer_id = customer.id
            JOIN product ON customer_product.product_id = product.id
            WHERE customer.id=?";

    $prep = $con->prepare($sql);
    $prep->execute([2]);
    $connectContent = $prep->fetchAll();

?>

<form method="post">
    <h2>INSERT</h2>
    <label>Name: </label><input type="text" name="productInsert[name]"><br>
    <label>Description: </label><textarea name="productInsert[description]"></textarea><br>
    <label>Type: </label><input type="text" name="productInsert[type]"><br>
    <label>Archived: </label><input type="checkbox" name="productInsert[archived]"><br>
    <input type="submit">
</form>

<form method="post">
    <h2>UPDATE</h2>
    <label>Id: </label><input type="text" name="productUpdate[id]"><br>
    <label>Name: </label><input type="text" name="productUpdate[name]"><br>
    <label>Description: </label><textarea name="productUpdate[description]"></textarea><br>
    <label>Type: </label><input type="text" name="productUpdate[type]"><br>
    <label>Archived: </label><input type="checkbox" name="productUpdate[archived]"><br>
    <input type="submit">
</form>

<form method="post">
    <h2>DELETE</h2>
    <label>Id: </label><input type="text" name="productDelete[id]"><br>
    <input type="submit">
</form>