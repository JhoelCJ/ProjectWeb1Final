<?php
session_start();
date_default_timezone_set("America/Guayaquil");

if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    echo "<script>
        alert('Tu sesión ha expirado por inactividad (3 minutos). Por favor, ingresa nuevamente.');
        window.history.replaceState(null, null, window.location.pathname);
    </script>";
}

$ruta = isset($_GET['ruta']) ? $_GET['ruta'] : 'login';

$rutas = [
    'login'          => 'paginas/login.html',
    'validar-login'  => 'php/loginValido.php',
    'inicio'         => 'main.php',  
    'salir'          => 'php/cerrarSesion.php',
    
    'usuarios'       => 'paginas/visualizacionUs.php',
    'crear-usuario'  => 'paginas/usuario.php',
    'guardar-usuario'=> 'php/usuario/insercion.php',
    'buscar-usuario' => 'paginas/busquedaUs.php',
    'editar-usuario' => 'paginas/editar.php',
    
    // Rutas Académicas
    'curso'          => 'paginas/visualizacionCursos.php',
    'auditoria'      => 'paginas/verAuditoria.php',
    'listado-alumnos' => 'paginas/listadoAlumnos.php',
    'reportes'        => 'paginas/reportes.php'
];

if (array_key_exists($ruta, $rutas)) {
    if ($ruta != 'login' && $ruta != 'validar-login' && !isset($_SESSION['usuario'])) {
        header('Location: index.php?ruta=login');
        exit;
    }

    include_once $rutas[$ruta];
} else {
    header('Location: index.php?ruta=login');
}
?>

<?php if ($ruta == 'login') { ?>
<script type="text/javascript">
    (function(){
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1); 
        };
    })();
</script>
<?php } ?>