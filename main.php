<?php
include_once("php/validarSesion.php");
include_once("php/conexion.php");
include_once("php/verificarPermiso.php");
date_default_timezone_set("America/Guayaquil");

$rolUsuario = isset($_SESSION['rol']) ? trim($_SESSION['rol']) : '';
$esAdmin = (strcasecmp($rolUsuario, 'Administrador') === 0);
$esAlumno = (isset($_SESSION['tipo_sesion']) && $_SESSION['tipo_sesion'] == 'alumno');
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>SISTEMA EMPRESARIAL ACADEMICO</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <link rel="stylesheet" href="css/estiloEmpresarial.css">
    <link rel="stylesheet" href="css/font-awesome.min.css">
    <link rel="stylesheet" href="css/visualCss.css">
    <link rel="stylesheet" href="css/formularioCss.css">

    <script src="js/bloqueoRetroceso.js"></script>
</head>
<body>

    <nav class="navbar">
        <div class="navbar-brand">
            SISTEMA EMPRESARIAL ACADEMICO
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item" onclick="mostrarInicio()">
                Inicio
            </li>

            <?php if (!$esAlumno) { ?>

                <?php if ($esAdmin || tienePermiso('ver_usuarios')) { ?>
                <li class="nav-item">
                    Usuarios
                    <div class="dropdown-content">
                        <?php if ($esAdmin) { ?>
                            <a href="#" onclick="cargarTab('paginas/usuario.php')">Registrar Usuario</a>
                        <?php } ?>
                        <?php if (tienePermiso('ver_usuarios')) { ?>
                            <a href="#" onclick="cargarTab('paginas/visualizacionUs.php')">Directorio de Usuarios</a>
                        <?php } ?>
                        <a href="#" onclick="cargarTab('paginas/busquedaUs.php')">Buscar Usuario</a>
                    </div>
                </li>
                <?php } ?>

                <?php if (tienePermiso('gestionar_alumnos') || tienePermiso('gestionar_cursos')) { ?>
                <li class="nav-item">
                    Académico
                    <div class="dropdown-content">
                        <?php if (tienePermiso('gestionar_alumnos')) { ?>
                            <a href="#" onclick="cargarTab('paginas/alumno.php')">Registrar Alumno</a>
                            <a href="#" onclick="cargarTab('paginas/listadoAlumnos.php')">Listado de Alumnos</a>
                        <?php } ?>
                        <?php if (tienePermiso('gestionar_cursos')) { ?>
                            <a href="#" onclick="cargarTab('paginas/visualizacionCursos.php')">Gestión de Cursos</a>
                            <a href="#" onclick="cargarTab('paginas/asignarCurso.php')">Matriculación</a>
                            <a href="#" onclick="cargarTab('paginas/gestionNotas.php')">Calificaciones</a>
                        <?php } ?>
                    </div>
                </li>
                <?php } ?>

                
                
                <?php if ($esAdmin || tienePermiso('gestionar_roles')) { ?>
                    <li class="nav-item" onclick="cargarTab('paginas/gestionarRoles.php')">
                        Configuración
                    </li>
                <?php } ?>

                <?php if ($esAdmin) { ?>
                    <li class="nav-item" onclick="cargarTab('paginas/verAuditoria.php')">
                        Auditoría
                    </li>
                <?php } ?>
                
                <?php if ($esAdmin || tienePermiso('ver_reportes')) { ?>
                    <li class="nav-item" onclick="cargarTab('paginas/reportes.php')">
                        Reportes
                    </li>
                <?php } ?>

            <?php } else { ?>
            
            <li class="nav-item" onclick="cargarTab('paginas/misNotas.php')">
                    Mis Notas
                </li>
                <li class="nav-item" onclick="cargarTab('paginas/inscripcionCurso.php')">
                    Inscribirse
                </li>
                <li class="nav-item" onclick="cargarTab('paginas/perfil.php')">
                    Mi Perfil
                </li>

            <?php } ?>
        </ul>

        <div class="nav-user-info">
            <?php if (!$esAlumno) { ?>
                <a href="#" onclick="cargarTab('paginas/miPerfil.php')" title="Mi Perfil">
                    <b><?php echo htmlspecialchars($_SESSION['usuario']); ?></b>
                </a>
            <?php } else { ?>
                <a href="#" onclick="cargarTab('paginas/perfil.php')" title="Mi Perfil">
                    <b><?php echo htmlspecialchars($_SESSION['usuario']); ?></b>
                </a>
            <?php } ?>

            <span>| <?php echo htmlspecialchars($_SESSION['rol']); ?> | <span id="reloj"></span></span>
            <a href="php/cerrarSesion.php" class="btn-salir">Salir</a>
        </div>
    </nav>

    <div class="main-container">

        <div id="contenido-dinamico" style="display:none; padding: 20px;">
        </div>

        <div id="seccion-inicio">

            <div class="container">
                <div class="text-center" style="margin-top: 40px;">
                    <h2 class="section-title">Bienvenido al Sistema Académico</h2>
                    <p style="color: #666; max-width: 700px; margin: 0 auto;">
                        Seleccione una opción del menú o utilice los accesos directos.
                    </p>
                </div>

                <div class="dashboard-grid">
                    
                    <?php if (!$esAlumno) { ?>
                        <?php if ($esAdmin || tienePermiso('ver_usuarios')) { ?>
                        <div class="card" onclick="cargarTab('paginas/visualizacionUs.php')" style="cursor:pointer;">
                            <h3>Gestión de Usuarios</h3>
                            <p>Administre cuentas del personal.</p>
                        </div>
                        <?php } ?>

                        <?php if ($esAdmin || tienePermiso('gestionar_cursos')) { ?>
                        <div class="card" onclick="cargarTab('paginas/gestionNotas.php')" style="cursor:pointer;">
                            <h3>Rendimiento</h3>
                            <p>Registro de calificaciones y actas.</p>
                        </div>
                        <?php } ?>

                    <?php } else { ?>
                        
                        <div class="card" onclick="cargarTab('paginas/alumno/misNotas.php')" style="cursor:pointer;">
                            <h3>Mis Calificaciones</h3>
                            <p>Revise sus notas parciales y promedios.</p>
                        </div>

                        <div class="card" onclick="cargarTab('paginas/alumno/inscripcionCurso.php')" style="cursor:pointer;">
                            <h3>Inscripción</h3>
                            <p>Matricúlese en nuevos cursos disponibles.</p>
                        </div>

                        <div class="card" onclick="cargarTab('paginas/alumno/perfil.php')" style="cursor:pointer;">
                            <h3>Mis Datos</h3>
                            <p>Actualice su información de contacto.</p>
                        </div>

                    <?php } ?>

                </div>
            </div>
            <div>
        </div>

    </div>

    <footer class="footer">
                &copy; <?php echo date("Y"); ?> SISTEMA EMPRESARIAL ACADEMICO. Todos los derechos reservados.<br>
                Soporte Técnico: soporte@empresa.com
    </footer>

    <script src="js/scriptReloj.js"></script>
    <script src="js/control_main.js"></script>
    <script src="js/funcionalidad_usuario.js"></script>
    <script src="js/funcionalidad_academica.js"></script>

    <script>
        const tiempoInactividad = 3 * 60 * 1000; 
        let temporizador;
        function reiniciarTemporizador() {
            clearTimeout(temporizador);
            temporizador = setTimeout(cerrarSesionAutomaticamente, tiempoInactividad);
        }
        function cerrarSesionAutomaticamente() {
            window.location.href = 'php/cerrarSesion.php'; 
        }
        window.onload = reiniciarTemporizador;
        document.onmousemove = reiniciarTemporizador;
        document.onkeypress = reiniciarTemporizador;
        document.onclick = reiniciarTemporizador;
        document.onscroll = reiniciarTemporizador;
    </script>

</body>
</html>