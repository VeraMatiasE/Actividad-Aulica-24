<?php
session_start();
$titulo = "Concurso de Disfrases";
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

            $sql = "SELECT nombre, descripcion, votos, foto, foto_blob FROM disfraces 
                        WHERE eliminado=0
                        ORDER BY votos DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $disfrases = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($disfrases as $indice => $disfraz) {
                ?>
                <?php if ($indice != 0)
                    echo "<hr>"; ?>
                <div class="disfraz">
                    <h2><?php echo htmlspecialchars($disfraz["nombre"]) ?></h2>
                    <p><?php echo htmlspecialchars($disfraz["descripcion"]) ?></p>
                    <?php

                    if ($disfraz["foto"] != "" && $disfraz["foto_blob"] != "") {
                        $nombre_imagen = htmlspecialchars($disfraz["foto"]);
                        $separacion = explode(".", $nombre_imagen);
                        $extension = end($separacion);

                        echo "<img src='data:image/$extension;base64," . base64_encode($disfraz["foto_blob"]) . "' witdh='100%'>";
                    }
                    ?>
                    <p><span>Votos:</span> <?php echo $disfraz["votos"] ?></p>
                    <button class="votar" <?php echo empty($_SESSION["id"]) ? "disabled" : "" ?>>Votar</button>
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