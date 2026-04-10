<?php
include_once("conexion.php");
include_once("validarSesion.php");

if ($_SESSION['rol'] != 'Administrador' && !in_array('ver_usuarios', $_SESSION['permisos'])) {
    echo "<tr><td colspan='7' class='w3-text-red w3-center'>No tienes permiso.</td></tr>";
    exit;
}

$criterio = isset($_POST['criterio']) ? $conn->real_escape_string($_POST['criterio']) : '';
$pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;
$registrosPorPagina = 5;
$offset = ($pagina - 1) * $registrosPorPagina;

$sqlCount = "SELECT COUNT(*) as total FROM usuarios u 
             WHERE u.nombre_user LIKE '%$criterio%' 
             OR u.apellido_user LIKE '%$criterio%' 
             OR u.cedula_user LIKE '%$criterio%' 
             OR u.usuario_user LIKE '%$criterio%'";
$resCount = $conn->query($sqlCount);
$rowCount = $resCount->fetch_assoc();
$totalRegistros = $rowCount['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT u.*, r.nombre_rol 
        FROM usuarios u 
        LEFT JOIN rol r ON u.id_rol = r.id_rol 
        WHERE u.nombre_user LIKE '%$criterio%' 
           OR u.apellido_user LIKE '%$criterio%' 
           OR u.cedula_user LIKE '%$criterio%' 
           OR u.usuario_user LIKE '%$criterio%'
        ORDER BY u.apellido_user ASC
        LIMIT $offset, $registrosPorPagina";

$res = $conn->query($sql);

if ($res && $res->num_rows > 0) {

    $idUsuarioSesion = $_SESSION['id_user'];
    $esAdminGeneral = ($_SESSION['rol'] == 'Administrador');

    while ($f = $res->fetch_assoc()) {
        $id = $f['id_user'];
        $nom = htmlspecialchars($f['nombre_user']);
        $ape = htmlspecialchars($f['apellido_user']);
        $user = htmlspecialchars($f['usuario_user']);
        $ced = htmlspecialchars($f['cedula_user']);

        $nombreRol = ($f['nombre_rol']) ? $f['nombre_rol'] : "<span class='w3-text-red'>Sin Asignar</span>";
        $rolJS = ($f['id_rol']) ? $f['id_rol'] : 0;
        
        $estado = strtolower($f['estado_user']);
        $colorTexto = ($estado == 'activo') ? 'green' : 'red';

        $esSuperAdmin = ($id == 1);

        echo "<tr>
                <td>{$f['cedula_user']}</td>
                <td>{$f['nombre_user']} {$f['apellido_user']}</td>
                <td>{$f['usuario_user']}</td>
                <td>$nombreRol</td>
                <td style='color:$colorTexto; font-weight:bold;'>".strtoupper($estado)."</td>
                <td>";
        
        if ($esAdminGeneral) {
            if ($esSuperAdmin) {
                echo "<span class='w3-tag w3-black w3-round' style='font-size:0.7rem;'>
                        SUPER ADMIN
                      </span>";
            } else {

                echo "<button class='w3-button w3-blue w3-tiny w3-round' title='Editar' style='margin-right:5px;' 
                        onclick='window.abrirModalEditarUsuario($id, \"$nom\", \"$ape\", \"$user\", \"$ced\", $rolJS)'>
                        Editar
                      </button> ";

                if ($id != $idUsuarioSesion) {
                    if ($estado == 'activo') {
                        echo "<button class='w3-button w3-red w3-tiny w3-round' onclick='window.eliminarUsuarioAjax($id)'>Desactivar</button>";
                    } else {
                        echo "<button class='w3-button w3-green w3-tiny w3-round' onclick='window.activarUsuarioAjax($id)'>Activar</button>";
                    }
                }
            }
        }
        echo "</td></tr>";
    }

    if ($totalPaginas > 1) {
        echo "<tr class='w3-light-grey'><td colspan='6' class='w3-center'>";

        if ($pagina > 1) {
            echo "<button class='w3-button w3-white w3-border' onclick='window.buscarUsuariosAjax(event, ".($pagina-1).")'>&laquo;</button> ";
        }

        for ($i = 1; $i <= $totalPaginas; $i++) {
            $estilo = ($i == $pagina) ? "w3-blue" : "w3-white";
            echo "<button class='w3-button $estilo w3-border' onclick='window.buscarUsuariosAjax(event, $i)'>$i</button> ";
        }

        if ($pagina < $totalPaginas) {
            echo "<button class='w3-button w3-white w3-border' onclick='window.buscarUsuariosAjax(event, ".($pagina+1).")'>&raquo;</button>";
        }
        
        echo "<br><span class='w3-tiny'>Página $pagina de $totalPaginas (Total: $totalRegistros)</span>";
        echo "</td></tr>";
    }

} else {
    echo "<tr><td colspan='6' class='w3-center'>No se encontraron coincidencias.</td></tr>";
}
?>