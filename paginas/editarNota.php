<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

exigirPermiso('gestionar_cursos');
$idNota = intval($_GET['id']);

$sql = "SELECT n.*, u.nombre_user, u.apellido_user, c.nombre_curso 
        FROM notas n
        INNER JOIN usuarios u ON n.id_user = u.id_user
        INNER JOIN cursos c ON n.id_curso = c.id_curso
        WHERE n.id_nota = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idNota);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();

if (!$data) {
    header("Location: gestionNotas.php");
    exit;
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Calificar Alumno</title>
<link href="../css/formularioCss.css" rel="stylesheet">
<link href="../css/w3.css" rel="stylesheet">
<script src="../js/bloqueoRetroceso.js"></script>
</head>
<body onload="window.calcularPromedio()">

<nav class="menu"><a href="gestionNotas.php">VOLVER</a></nav>

<div class="contenedor">
    <h2>Calificar: <?php echo $data['nombre_curso']; ?></h2>
    <h3>Alumno: <?php echo $data['apellido_user'] . " " . $data['nombre_user']; ?></h3>
    <hr>

    <form method="post" action="../php/notas/actualizarNota.php">
        <input type="hidden" name="id_nota" value="<?php echo $data['id_nota']; ?>">

        <div class="w3-row-padding">
            <div class="w3-third">
                <label>Nota 1 (0-20):</label>
                <input type="number" id="nota1" name="nota1" step="0.01" min="0" max="20" 
                       value="<?php echo $data['nota1']; ?>" oninput="window.calcularPromedio()" required>
            </div>
            <div class="w3-third">
                <label>Nota 2 (0-20):</label>
                <input type="number" id="nota2" name="nota2" step="0.01" min="0" max="20" 
                       value="<?php echo $data['nota2']; ?>" oninput="window.calcularPromedio()" required>
            </div>
            <div class="w3-third">
                <label>Nota 3 (0-20):</label>
                <input type="number" id="nota3" name="nota3" step="0.01" min="0" max="20" 
                       value="<?php echo $data['nota3']; ?>" oninput="window.calcularPromedio()" required>
            </div>
        </div>

        <p>
            <label>Promedio Calculado:</label>
            <input type="text" id="promedio_vista" readonly style="background:#eee; font-weight:bold;">
            <span id="mensaje_estado"></span>
        </p>

        <div id="campo_supletorio" style="display:none; border:1px solid orange; padding:10px; margin-top:10px;">
            <label style="color:red; font-weight:bold;">Examen Supletorio (Mínimo 14):</label>
            <input type="number" id="supletorio" name="supletorio" step="0.01" min="0" max="20" 
                   placeholder="Ingrese nota supletorio" value="<?php echo $data['supletorio']; ?>">
            <small>Si el alumno reprueba por promedio, ingrese aquí la nota del examen de recuperación.</small>
        </div>

        <br>
        <input type="submit" value="Guardar Calificaciones">
    </form>
</div>
<script src="../js/control_main.js"></script>

</body>
</html>