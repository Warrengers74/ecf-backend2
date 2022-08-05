<?php

try {
    // Conexion à la bdd
    $db = new PDO('mysql:host=localhost;dbname=ecfbackend2', 'root', '');
    $db->exec('SET NAMES "UTF8"');
}
catch(PDOException $e) {
    echo 'Erreur : ' . $e->getMessage();
    die();
}