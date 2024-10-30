<?php
session_start();
$titulo = "Administración";
include_once "../include/head.php";
require_once "../include/basededatos.php";

if (!isset($_GET['accion']))
    $_GET['accion'] = '';

if ($_GET['accion'] == 'guardar_insertar') {

}

if ($_GET['accion'] == 'guardar_editar') {
    $url = "paginas/admin.php?accion=guardar_editar&id=$_GET[id]";

    include_once "../include/basededatos.php";
    $pdo = conectarBaseDatos();
    $sql = "SELECT nombre, descripcion, foto, foto_blob FROM disfraces
            WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["id" => $_GET["id"]]);

    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    echo $resultado;

    //controlo si tengo que editar la foto
    if(is_uploaded_file($_FILES['foto']['tmp_name']))
    {
        //copiar en un directorio
        $nombre = explode('.', $_FILES['foto']['name']);
        $foto = time().'.'.end($nombre);
        copy($_FILES['foto']['tmp_name'], 'imagenes/'.$foto);

        //obtener el blob
        $image = $_FILES['foto']['tmp_name'];
        $imgContenido = addslashes(file_get_contents($image));

        //armo la cadena para editar las fotos
        $mas_datos = ", foto='".$foto."', foto_blob='".$imgContenido."'";
    }
        else
            $mas_datos = '';
    //fin de controlar si tengo que editar la foto
    $sql = "UPDATE disfraces SET nombre='{$_POST['nombre']}', descripcion='{$_POST['descripcion']}' {$mas_datos} WHERE id=".$_GET['id'];
    $sql = mysqli_query($con, $sql);
    if(!mysqli_error($con))
        echo "<script> alert('Disfraz editado con exito');</script>";
    else
        echo "<script> alert('ERROR NO SE PUDO editar EL DISFRAZ);</script>";
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
            <form action="procesar_disfraz.php" method="POST">
                <label for="disfraz-nombre">Nombre del Disfraz:</label>
                <input type="text" id="disfraz-nombre" name="disfraz-nombre" required>

                <label for="disfraz-descripcion">Descripción del Disfraz:</label>
                <textarea id="disfraz-descripcion" name="disfraz-descripcion" required></textarea>

                <label for="disfraz-nombre">Foto:</label>
                <input type="file" id="disfraz-foto" name="disfraz-foto" required>

                <?php
                if (!empty($resultado["foto"])) {
                    echo "<img >";
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
                                <a href="/paginas/admin.php?accion=editar&id=<?php echo $resultado['id']; ?>">Editar</a>
                                -
                                <a
                                    href='javascript:if(confirm("Desea eliminar el registro?"))window.location="/paginas/admin.php?accion=guardar_eliminar&id=<?php echo $resultado["id"] . '"'; ?>'>Eliminar</a>
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