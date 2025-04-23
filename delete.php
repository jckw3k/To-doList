<?php
    if ( isset($_GET["id"]) ){
        $id = $_GET["id"];

        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "webprogramming";

        // create connection
        $connection = new mysqli($servername, $username, $password, $database);

        $sql = "DELETE FROM tasks WHERE taskID = $id";
        $connection->query($sql); //to execute sql query
        
    }
    header("location: index.php");
    exit;
?>