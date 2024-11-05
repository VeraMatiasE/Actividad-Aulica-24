<?php
session_start();
$titulo = "Administración";
$relative_path = "..";
$close_head = false;
include_once "../include/head.php";
require_once "../include/basededatos.php";

if (!isset($_GET['accion']))
    $_GET['accion'] = '';

if ($_GET['accion'] == 'guardar_insertar') {
    $nombre = $_POST['nombre'];
    $sql = "SELECT *FROM disfraces where nombre = :nombre";
    $pdo = conectarBaseDatos();
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["nombre" => $nombre]);

    if ($stmt->rowCount() != 0) {
        echo "<script> alert('EL DISFRAZ YA EXISTE EN LA BD');</script>";
    } else {
        $foto = '';
        $imgContenido = '';
        //procesar la foto
        if (is_uploaded_file($_FILES['foto']['tmp_name'])) {
            //copiar en un directorio
            $nombre_img = explode('.', $_FILES['foto']['name']);
            $foto = time() . '.' . end($nombre_img);
            copy($_FILES['foto']['tmp_name'], '../imagenes/' . $foto);

            //obtener el blob
            $image = $_FILES['foto']['tmp_name'];
            $imgContenido = addslashes(file_get_contents($image));
        }

        try {
            $descripcion = $_POST['descripcion'];
            $sql = "INSERT INTO disfraces (nombre,descripcion,votos,foto,foto_blob) values(:nombre,:descripcion,0,:foto,:foto_blob)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(["nombre" => $nombre, "descripcion" => $descripcion, "foto" => $foto, "foto_blob" => $imgContenido]);
            echo "<script> alert('Disfraz cargado con exito');</script>";
        } catch (PDOException $e) {
            echo "<script defer> alert('ERROR NO SE PUDO INSERTAR EL DISFRAZ');</script>";
        }
    }
}

if ($_GET['accion'] == 'guardar_editar') {
    $url = "paginas/admin.php?accion=guardar_editar&id=$_GET[id]";
    $pdo = conectarBaseDatos();
    $sql = "SELECT nombre, descripcion, foto, foto_blob FROM disfraces
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id" => $_GET["id"]]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    //controlo si tengo que editar la foto
    if (is_uploaded_file($_FILES['foto']['tmp_name'])) {
        //copiar en un directorio
        $nombre = explode('.', $_FILES['foto']['name']);
        $foto = time() . '.' . end($nombre);
        copy($_FILES['foto']['tmp_name'], '../imagenes/' . $foto);

        //obtener el blob
        $image = $_FILES['foto']['tmp_name'];
        $imgContenido = addslashes(file_get_contents($image));

        //armo la cadena para editar las fotos
        $mas_datos = ", foto='" . $foto . "', foto_blob='" . $imgContenido . "'";
    } else
        $mas_datos = '';
    //fin de controlar si tengo que editar la foto
    try {
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $id = $_GET['id'];
        $sql = "UPDATE disfraces SET nombre=:nombre, descripcion=:descipcion {$mas_datos} WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['nombre' => $nombre, 'descipcion' => $descripcion, "id" => $id]);
        echo "<script> alert('Disfraz editado con exito');</script>";
    } catch (PDOException $e) {

        echo "<script> alert('ERROR NO SE PUDO editar EL DISFRAZ);</script>";
    }
}

if ($_GET['accion'] == 'guardar_eliminar') {
    $id = $_GET['id'];
    $sql = "UPDATE disfraces SET eliminado=1 WHERE id=:id";
    $pdo = conectarBaseDatos();

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue("id", $id, PDO::PARAM_INT);
        $stmt->execute();
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<script> alert('Disfraz eliminado con exito');</script>";
    } catch (PDOException $e) {
        echo "<script> alert('ERROR NO SE PUDO eliminar EL DISFRAZ);</script>";
    }
}
?>

<body>
    <?php
    include_once "../include/nav.php";
    ?>
    <main>
        <section id="admin" class="section">
            <h2>Panel de Administración</h2>
            <?php
            if ($_GET['accion'] == 'editar') {
                $id = $_GET['id'];
                $url = "admin.php?accion=guardar_editar&id=$id";
                $sql = "SELECT * FROM disfraces WHERE id = :id";
                $pdo = conectarBaseDatos();
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue("id", $id, PDO::PARAM_INT);
                $stmt->execute();
                $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            } else {
                $url = 'admin.php?accion=guardar_insertar';
                $resultado['nombre'] = $resultado['descripcion'] = $resultado['foto'] = '';
            }
            ?>
            <form action="<?= $url ?>" method="POST" enctype="multipart/form-data">
                <label for="disfraz-nombre">Nombre del Disfraz:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo $resultado['nombre']; ?>" required>

                <label for="disfraz-descripcion">Descripción del Disfraz:</label>
                <textarea id="descripcion" name="descripcion"
                    required><?php echo $resultado['descripcion']; ?></textarea>

                <label for="disfraz-nombre">Foto:</label>
                <input type="file" id="foto" name="foto">

                <?php
                if (!empty($resultado['foto'])) {
                    ?>
                    <img src="../imagenes/<?php echo $resultado['foto']; ?>" width="100%">
                    <?php
                }
                ?>

                <button type="submit">Agregar Disfraz</button>
            </form>

        </section>
        <section id="listado" class="section">
            <h2>Listado</h2>
            <table border="1" style="width: 100%;">
                <tr>
                    <td>Item</td>
                    <td>Nombre</td>
                    <td>Opciones</td>
                </tr>
                <?php
                $sql = "SELECT id, nombre FROM disfraces WHERE eliminado=0 ORDER BY nombre";
                $pdo = conectarBaseDatos();
                $stmt = $pdo->prepare($sql);
                $stmt->execute();

                if ($stmt->rowCount() != 0) {
                    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($resultados as $resultado) {
                        ?>
                        <tr>
                            <td><?php echo $resultado['id']; ?></td>
                            <td align="left"><?php echo $resultado['nombre']; ?></td>
                            <td>
                                <a href="admin.php?accion=editar&id=<?php echo $resultado['id']; ?>">Editar</a>
                                -
                                <a
                                    href='javascript:if(confirm("Desea eliminar el registro?"))window.location="admin.php?accion=guardar_eliminar&id=<?php echo $resultado["id"] . '"'; ?>'>Eliminar</a>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </table>
        </section>
    </main>
</body>

</html>