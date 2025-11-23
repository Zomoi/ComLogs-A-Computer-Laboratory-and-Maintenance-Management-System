<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$pdo = new PDO("mysql:host=localhost;dbname=comlogs_db;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>