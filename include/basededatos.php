<?php

function conectarBaseDatos()
{
    $host = "localhost";
    $database = "halloween";
    $dsn = "mysql:host=$host;dbname=$database";
    $usuario = "paradigmas3";
    $contrasena = "lX0JNLFfC99dvhJa";
    try {
        $pdo = new PDO($dsn, $usuario, $contrasena);
    } catch (PDOException $e) {
        exit();
    }

    return $pdo;
}

?>