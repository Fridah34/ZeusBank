<?php
$dsn = "mysql:host=localhost;dbname=bank_system";
$dbusername = "root"; // default username for MySQL
$dbpassword = ""; // default password for MySQL (if using XAMPP or WAMP)


try{
    $con =new PDO($dsn,$dbusername,$dbpassword);
    $con ->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    echo "";

    $conn = new mysqli("localhost", $dbusername, $dbpassword, "bank_system");
    if ($conn->connect_error) {
        throw new Exception("MySQLi connection failed: " . $conn->connect_error);
    }

    
} catch (PDOException $e) {
    echo "Connection failed: ".$e->getMessage();

}

