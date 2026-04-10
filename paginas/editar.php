<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

exigirPermiso('editar_usuario'); 

if (!isset($_GET['id'])) {
    echo "Error: ID no proporcionado.";
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT * FROM usuarios WHERE id_user = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$u = $result->fetch_assoc();

if (!$u) {
    echo "Usuario no encontrado.";
    exit;
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Editar Usuario</title>
<link href="../css/formularioCss.css" rel="stylesheet">
<style>
    .link-crear {
        text-decoration: none;
        color: #fff;
        background-color: #2196F3;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        margin-left: 10px;
    }
    .link-crear:hover {
        background-color: #0b7dda;
    }
</style>
<script src="../js/bloqueoRetroceso.js"></script>
</head>

<body>

<nav class="menu">
    <a href="#" onclick="cargarTab('paginas/visualizacionUs.php')">VOLVER</a>
</nav>

<div class="contenedor">
    <h1>Editar Usuario</h1>

    <form method="post" action="php/usuario/actualizar.php" onsubmit="window.actualizarUsuarioAjax(event)">
        
        <fieldset>
            <input type="hidden" name="id_user" value="<?= $u['id_user'] ?>">

            <p>
                <label>Nombre:</label>
                <input type="text" name="nombre" value="<?= $u['nombre_user'] ?>" required>
            </p>

            <p>
                <label>Apellido:</label>
                <input type="text" name="apellido" value="<?= $u['apellido_user'] ?>" required>
            </p>

            <p>
                <label>Usuario (Login):</label>
                <input type="text" name="usuario" value="<?= $u['usuario_user'] ?>" readonly>
            </p>

            <p>
                <label>Cédula:</label>
                <input type="text" name="cedula" value="<?= $u['cedula_user'] ?>" readonly>
            </p>

            <p>
                <label>Nueva Clave:</label>
                <input type="password" name="clave_nueva" placeholder="Dejar vacío para mantener la actual">
            </p>
            
            <p>
                <label>Rol:</label>
                <span style="display:flex; align-items:center;">
                    <select name="rol" required style="flex-grow:1;">
                        <option value="">-- Seleccione un rol --</option>
                        <?php
                        $sqlRoles = "SELECT id_rol, nombre_rol FROM rol";
                        $resRoles = $conn->query($sqlRoles);
                        while($row = $resRoles->fetch_assoc()) {
                            $selected = ($u['id_rol'] == $row['id_rol']) ? 'selected' : '';
                            echo "<option value='{$row['id_rol']}' $selected>{$row['nombre_rol']}</option>";
                        }
                        ?>
                    </select>

                    <?php if (tienePermiso('gestionar_roles')) { ?>
                        <a href="#" onclick="cargarTab('paginas/gestionarRoles.php')" class="link-crear">
                            + Roles
                        </a>
                    <?php } ?>
                </span>
            </p>

        </fieldset>
        
        <div style="text-align:center; margin-top:20px;">
            <button type="submit" class="w3-button w3-blue w3-round">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

</body>
</html>