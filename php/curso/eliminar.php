<?php
include_once("../conexion.php");
include_once("../validarSesion.php");
include_once("../validarRol.php");
include_once("../logger.php");

validarRol(['Administrador', 'Supervisor']);

$id = intval($_GET['id']);
$accion = isset($_GET['accion']) ? $_GET['accion'] : 'desactivar';

$nuevoEstado = ($accion == 'activar') ? 'activo' : 'inactivo';

$sql = "UPDATE cursos SET estado_curso=? WHERE id_curso=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $nuevoEstado, $id);

if ($stmt->execute()) {
    registrarAuditoria($conn, "Estado Curso", "Curso ID: $id cambio a: $nuevoEstado");
    echo "<script>
        window.location.href='../../paginas/visualizacionCursos.php';
    </script>";
} else {
    echo "Error: " . $stmt->error;
}
?>