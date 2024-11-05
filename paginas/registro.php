<?php
$titulo = "Registro";
$relative_path = "..";
$close_head = true;
include_once "../include/head.php";

if (isset($_POST["username"]) && isset($_POST["password"])) {
    include_once "../include/basededatos.php";
    $pdo = conectarBaseDatos();

    $username = $_POST["username"];

    $sql = "SELECT true FROM usuarios WHERE nombre = :nombre";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["nombre" => $username]);
    $existe_usuario = $stmt->fetchColumn();

    if ($existe_usuario) {
        echo "<script>alert('Error: El usuario ya existe en la base de datos')</script>";
    } else {
        $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

        $sql = "INSERT INTO usuarios(nombre, clave) VALUE(:username, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(["username" => $username, "password" => $password]);

        header("Location: ../index.php");
        exit();
    }
}

?>

<body>
    <?php
    include_once "../include/nav.php";
    ?>
    <main id="registro" class="section">
        <h2>Registro</h2>
        <form action="registro.php" method="POST">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Registrarse</button>
        </form>
    </main>
</body>

</html>