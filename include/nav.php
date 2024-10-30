<nav>
    <ul>
        <li><a href="/">Ver Disfraces</a></li>
        <li><a href="/paginas/registro.php">Registro</a></li>
        <li><a href="/paginas/login.php">Iniciar Sesión</a></li>
        <li><a href="/paginas/admin.php">Panel de Administración</a></li>
        <?php echo isset($_SESSION["id"]) ? "<li><a href='/paginas/login.php?salir=ok'>Salir</a></li>" : "" ?>
    </ul>
</nav>