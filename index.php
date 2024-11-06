<?php
session_start();
$titulo = "Concurso de Disfrases";
$relative_path = ".";
$close_head = true;
include_once "include/head.php";

?>

<body>
    <?php
    include_once "include/nav.php";
    ?>
    <header>
        <h1>Concurso de disfraces de Halloween</h1>
    </header>
    <main>
        <section id="disfraces-list" class="section">
            <!-- Aquí se mostrarán los disfraces -->
            <?php
            include_once "include/basededatos.php";

            $pdo = conectarBaseDatos();

            $sql = "SELECT id, nombre, descripcion, votos, foto, foto_blob FROM disfraces
                        WHERE eliminado=0
                        ORDER BY votos DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $disfrases = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (isset($_SESSION["id"])) {
                $id_usuario = $_SESSION["id"];
                $sql = "SELECT id_disfraz FROM votos WHERE id_usuario = :id_usuario";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(["id_usuario" => $id_usuario]);
                $votos = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } else {
                $votos = [];
            }

            foreach ($disfrases as $indice => $disfraz) {
                ?>
                <?php if ($indice != 0)
                    echo "<hr>"; ?>
                <div class="disfraz">
                    <h2 class="nombre"><?php echo htmlspecialchars($disfraz["nombre"]) ?></h2>
                    <p><?php echo htmlspecialchars($disfraz["descripcion"]) ?></p>
                    <?php

                    if ($disfraz["foto"] != "") {
                        if (file_exists('imagenes/' . $disfraz['foto'])) {
                            ?>
                            <p><img src="imagenes/<?php echo $disfraz['foto']; ?>" width="100%"></p> <?php
                        } else if ($disfraz["foto_blob"] != "") {
                            $nombre_imagen = htmlspecialchars($disfraz["foto"]);
                            $separacion = explode(".", $nombre_imagen);
                            $extension = end($separacion);
                            echo "<img src='data:image/$extension;base64," . base64_encode(stripslashes($disfraz["foto_blob"])) . "' witdh='100%'>";
                        }
                    } else {
                        echo "<p>Sin fotos</p>";
                    }
                    ?>
                    <p class="votos"><span>Votos:</span> <?php echo $disfraz["votos"] ?></p>
                    <button class="votar" <?php echo empty($_SESSION["id"]) || in_array($disfraz["id"], $votos) ? "disabled" : "" ?>>Votar</button>
                </div>
                <?php
            }
            ?>

        </section>
    </main>
    <?php
    if (isset($_SESSION["id"])) {
        echo '<script src="js/script.js"></script>';
    }
    ?>
</body>

</html>