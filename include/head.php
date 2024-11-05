<!DOCTYPE html>
<html lang="es">

<?php

if (!isset($titulo)) {
    $titulo = "Docuemnt";
}

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo ?></title>
    <link rel="stylesheet" href="<?= $relative_path ?>/css/estilos.css">
    <?php if ($close_head) { ?>
    </head>
<?php } ?>