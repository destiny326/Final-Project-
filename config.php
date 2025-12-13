<?php
/* Database credentials.*/
define('DB_SERVER', 'mysql-6618668-destinyrma4-3d00.g.aivencloud.com');
define('DB_USERNAME', 'avnadmin');
define('DB_PASSWORD', 'AVNS_2QgIDfq3O9U2e_p9o5h');
define('DB_NAME', 'stagedb');
define('DB_PORT', '22394');
 
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>