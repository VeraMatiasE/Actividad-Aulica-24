<nav>
    <ul>
        <li><a href="<?= $relative_path ?>/">Ver Disfraces</a></li>
        <li><a href="<?= $relative_path ?>/paginas/registro.php">Registro</a></li>
        <li><a href="<?= $relative_path ?>/paginas/login.php">Iniciar Sesión</a></li>
        <li><a href="<?= $relative_path ?>/paginas/admin.php">Panel de Administración</a></li>
        <?php echo isset($_SESSION["id"]) ? "<li><a href='$relative_path/paginas/login.php?salir=ok'>Salir</a></li>" : "" ?>
    </ul>
</nav>