<?php
// Check connection

$connection = new mysqli("localhost", "root", "", "vier_gewinnt");

if ($connection ->connect_error) {
    die("Connection failed: " . $connection ->connect_error);
   }
   else {
    $sql = " SELECT * FROM users ";
    $result = $connection ->query($sql);
    $row = $result->fetch_assoc();
            echo $row["vorname"];
   }
?>
