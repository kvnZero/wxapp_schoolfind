<?php
$sqlhost="localhost:3306"; //数据库地址
$sqluser="comet"; //数据库用户
$sqlpass="linux123!"; //数据库密码
$sqldata="find"; //数据库名字
$dsn="mysql:host=$sqlhost;dbname=$sqldata";
try {
    $db = new PDO($dsn, $sqluser, $sqlpass, array(PDO::ATTR_PERSISTENT => true));
    //$db->beginTransaction();
} catch (PDOException $e) {
    die ("Error!: " . $e->getMessage() . "<br/>");
}
?>
