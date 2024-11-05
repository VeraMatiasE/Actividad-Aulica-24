<?php
session_start();

if (!isset($_SESSION["id"])) {
    http_response_code(400);
    exit();
}

include_once "../include/basededatos.php";

try {
    // Verifica si existe el disfraz y no halla sido eliminado
    $nombre = $_POST["nombre"];
    $sql = "SELECT id FROM disfraces WHERE nombre = :nombre AND eliminado != 1";
    $pdo = conectarBaseDatos();
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["nombre" => $nombre]);

    if ($stmt->rowCount() == 0) {
        http_response_code(400);
        exit();
    }
    $id_usuario = $_SESSION["id"];
    $id_disfraz = $stmt->fetchColumn();

    // Verifica que el usuario ya halla votado
    $sql = "SELECT EXISTS (SELECT 1 FROM votos WHERE id_usuario = :id_usuario AND id_disfraz = :id_disfraz) AS existe";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id_usuario" => $id_usuario, "id_disfraz" => $id_disfraz]);
    $existe_voto = $stmt->fetchColumn();

    if ($existe_voto) {
        http_response_code(200);
        echo json_encode(["message" => "Ya has votado por este disfraz."]);
        exit();
    }
    // Inserta el voto
    $sql = "INSERT INTO votos(id_usuario, id_disfraz) VALUES(:id_usuario, :id_disfraz)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id_usuario" => $id_usuario, "id_disfraz" => $id_disfraz]);

    $sql = "UPDATE disfraces SET votos = votos + 1 WHERE id = :id_disfraz";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id_disfraz" => $id_disfraz]);
    echo json_encode(["message" => "Voto registrado con éxito."]);
} catch (PDOException $e) {
    http_response_code(500);
}
?>