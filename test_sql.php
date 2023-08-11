<html>
<head><title>PHP TEST</title></head>
<body>

<?php

$dsn = 'mysql:dbname=easelp;host=localhost';

$sql_user = "root";

$sql_password = "";

try{
    $dbh = new PDO($dsn, $sql_user, $sql_password);

    $result = $dbh->query("select * from users;");

    foreach ($result as $row) {
        print($row['id']);
        print($row['password'].'<br>');
    }

}catch (PDOException $e){
    print('Error:'.$e->getMessage());
    die();
}

$dbh = null;

?>

</body>