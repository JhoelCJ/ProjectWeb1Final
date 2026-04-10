<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");
include_once("../php/validarRol.php");

validarRol(['Administrador', 'Supervisor']);

$esEdicion = false;
$nombre = "";
$capacidad = "";
$instructor = "";
$especialidad = "";
$idCurso = "";

if (isset($_GET['id'])) {
    $esEdicion = true;
    $idCurso = intval($_GET['id']);
    $sql = "SELECT * FROM cursos WHERE id_curso = $idCurso";
    $res = $conn->query($sql);
    $data = $res->fetch_assoc();
    
    $nombre = $data['nombre_curso'];
    $capacidad = $data['capacidad'];
    $instructor = $data['instructor'];
    $especialidad = $data['especialidad'];
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title><?php echo $esEdicion ? 'Editar Curso' : 'Nuevo Curso'; ?></title>
<link href="../css/formularioCss.css" rel="stylesheet">
<script src="../js/bloqueoRetroceso.js"></script>
</head>
<body>

<nav class="menu"><a href="visualizacionCursos.php">VOLVER</a></nav>

<div class="contenedor">
    <h1><?php echo $esEdicion ? 'Editar Curso' : 'Registrar Nuevo Curso'; ?></h1>

    <form method="post" action="<?php echo $esEdicion ? '../php/curso/actualizar.php' : '../php/curso/insercion.php'; ?>">
        <fieldset>
            <?php if($esEdicion) { echo "<input type='hidden' name='id_curso' value='$idCurso'>"; } ?>

            <p>
                <label>Nombre del Curso:</label>
                <input type="text" name="nombre" value="<?php echo $nombre; ?>" required>
            </p>

            <p>
                <label>Especialidad:</label>
                <input type="text" name="especialidad" value="<?php echo $especialidad; ?>" required>
            </p>

            <p>
                <label>Instructor:</label>
                <input type="text" name="instructor" value="<?php echo $instructor; ?>" required placeholder="Nombre del Docente">
            </p>

            <p>
                <label>Capacidad (Estudiantes):</label>
                <input type="number" name="capacidad" value="<?php echo $capacidad; ?>" min="1" required>
            </p>

        </fieldset>

        <input type="submit" value="<?php echo $esEdicion ? 'Actualizar Curso' : 'Guardar Curso'; ?>">
    </form>
</div>

</body>
</html>