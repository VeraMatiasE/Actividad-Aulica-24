<?php
session_start();
$titulo = "Login";
$relative_path = "..";
$close_head = true;
include_once "../include/head.php";

if (isset($_GET["salir"])) {
    session_destroy();
    echo "<script>window.location='../index.php';</script>";
}

if (isset($_POST["login-username"]) && isset($_POST["login-password"])) {
    include "../include/basededatos.php";
    $pdo = conectarBaseDatos();

    $username = $_POST["login-username"];

    $sql = "SELECT id, nombre, clave FROM usuarios WHERE nombre = :username";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["username" => $username]);

    $resultado = $stmt->fetch();

    if ($resultado == false) {
        echo "<script>alert('Las credenciales son incorrectas')</script>";
    } else {
        $clave = $_POST["login-password"];
        $coincide_clave = password_verify($clave, $resultado["clave"]);
        if (!$coincide_clave) {
            echo "<script>alert('Las credenciales son incorrectas')</script>";
        } else {
            $_SESSION["id"] = $resultado["id"];
            $_SESSION["nombre_usuario"] = $resultado["nombre"];
            echo "<script>alert('Bienvenido $_SESSION[nombre_usuario]')</script>";
            header("Location: ../index.php");
            exit();
        }
    }
}
?>

<body>
    <?php
    include_once "../include/nav.php";
    ?>
    <main id="login" class="section">
        <h2>Iniciar Sesión</h2>
        <form action="login.php" method="POST">
            <label for="login-username">Nombre de Usuario:</label>
            <input type="text" id="login-username" name="login-username" required>

            <label for="login-password">Contraseña:</label>
            <input type="password" id="login-password" name="login-password" required>

            <button type="submit">Iniciar Sesión</button>
        </form>
    </main>
</body>

</html>