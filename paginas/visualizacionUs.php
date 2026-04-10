<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

if (!tienePermiso('ver_usuarios') && $_SESSION['rol'] != 'Administrador') {
    echo "<div class='w3-panel w3-red'><h3>Acceso Denegado</h3></div>"; 
    exit;
}

$registrosPorPagina = 5; 
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;

$offset = ($pagina - 1) * $registrosPorPagina;
$sqlTotal = "SELECT COUNT(*) as total FROM usuarios";
$resTotal = $conn->query($sqlTotal);
$rowTotal = $resTotal->fetch_assoc();
$totalRegistros = $rowTotal['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT u.*, r.nombre_rol 
        FROM usuarios u 
        LEFT JOIN rol r ON u.id_rol = r.id_rol 
        ORDER BY u.id_user ASC 
        LIMIT $offset, $registrosPorPagina";
$res = $conn->query($sql);
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1200px; margin:auto;">
        
        <h2 style="border-bottom: 2px solid #003366; color: #003366; display:flex; justify-content:space-between;">
            <span>Directorio de Usuarios</span>
            <?php if ($_SESSION['rol'] == 'Administrador') { ?>
            <button onclick="cargarTab('paginas/usuario.php')" class="w3-button w3-blue w3-small w3-round">
                Nuevo
            </button>
            <?php } ?>
        </h2>

        <div style="overflow-x:auto;">
            <table class="w3-table-all">
                <thead>
                    <tr class="w3-light-grey">
                        <th>ID</th> <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($res && $res->num_rows > 0) {
                    $idUsuarioSesion = $_SESSION['id_user'];
                    $esAdminGeneral = ($_SESSION['rol'] == 'Administrador');
                    $tienePermisoEditar = tienePermiso('editar_usuario');

                    while ($f = $res->fetch_assoc()) {
                        $id = $f['id_user'];
                        $nom = htmlspecialchars($f['nombre_user']);
                        $ape = htmlspecialchars($f['apellido_user']);
                        $user = htmlspecialchars($f['usuario_user']);
                        $ced = htmlspecialchars($f['cedula_user']);
                        
                        $nombreRol = ($f['nombre_rol']) ? $f['nombre_rol'] : "<span class='w3-text-red'>Sin Asignar</span>";
                        $idRolJS = ($f['id_rol']) ? $f['id_rol'] : 0;
                        
                        $estado = strtolower($f['estado_user']);
                        $colorTexto = ($estado == 'activo') ? 'green' : 'red';
                        $esSuperAdmin = ($id == 1);

                        echo "<tr>
                                <td>$id</td>
                                <td>$ced</td>
                                <td>$nom $ape</td>
                                <td>$user</td>
                                <td>$nombreRol</td>
                                <td style='color:$colorTexto; font-weight:bold;'>".strtoupper($estado)."</td>
                                <td>";

                        if ($esSuperAdmin) {
                            echo "<span class='w3-tag w3-black w3-round' style='font-size:0.7rem;'>
                                    SUPER ADMIN
                                  </span>";
                        } else {
                            $puedeEditar = $esAdminGeneral || ($tienePermisoEditar && $id == $idUsuarioSesion);

                            if ($puedeEditar) {
                                echo "<button class='w3-button w3-blue w3-tiny w3-round' title='Editar' style='margin-right:5px;'
                                    onclick='window.abrirModalEditarUsuario($id, \"$nom\", \"$ape\", \"$user\", \"$ced\", $idRolJS)'>
                                    Editar
                                    </button>";
                            } else {
                                echo "<span class='w3-small w3-text-grey'>--</span>";
                            }

                            if ($esAdminGeneral && $id != $idUsuarioSesion) {
                                if ($estado == 'activo') {
                                    echo "<button class='w3-button w3-red w3-tiny w3-round' onclick='window.eliminarUsuarioAjax($id)'>Desactivar</button>";
                                } else {
                                    echo "<button class='w3-button w3-green w3-tiny w3-round' onclick='window.activarUsuarioAjax($id)'>Activar</button>";
                                }
                            }
                        }
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' class='w3-center'>No hay usuarios registrados.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPaginas > 1) { ?>
        <div class="w3-center w3-padding-16">
            <div class="w3-bar w3-border w3-round w3-light-grey">
                <?php
                if ($pagina > 1) {
                    $pagAnt = $pagina - 1;
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/visualizacionUs.php?pagina=$pagAnt')\">&laquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&laquo;</button>";
                }
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    $claseActiva = ($i == $pagina) ? 'w3-blue' : 'w3-white';
                    echo "<button class='w3-bar-item w3-button $claseActiva' onclick=\"cargarTab('paginas/visualizacionUs.php?pagina=$i')\">$i</button>";
                }
                if ($pagina < $totalPaginas) {
                    $pagSig = $pagina + 1;
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/visualizacionUs.php?pagina=$pagSig')\">&raquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&raquo;</button>";
                }
                ?>
            </div>
            <div class="w3-small w3-text-grey w3-margin-top">
                Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?> (Total: <?php echo $totalRegistros; ?>)
            </div>
        </div>
        <?php } ?>

    </div>

    <div id="modalEditarUs" class="modal-overlay">
        <div class="modal-content-corp">
            <div class="modal-header">
                <h3>Editar Usuario</h3>
                <span class="close-btn" onclick="document.getElementById('modalEditarUs').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <form onsubmit="window.actualizarUsuarioAjax(event)" action="php/usuario/actualizar.php">
                    <input type="hidden" name="id_user" id="e_id_user">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" id="e_nombre" class="w3-input w3-border" required>
                        </div>
                        <div class="form-group">
                            <label>Apellido:</label>
                            <input type="text" name="apellido" id="e_apellido" class="w3-input w3-border" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Usuario (Login):</label>
                            <input type="text" name="usuario" id="e_usuario" class="w3-input w3-border" readonly>
                        </div>
                        <div class="form-group">
                            <label>Cédula:</label>
                            <input type="text" name="cedula" id="e_cedula" class="w3-input w3-border" maxlength="10" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Rol:</label>
                        <select name="rol" id="e_rol" class="w3-select w3-border" required>
                            <option value="" disabled selected>-- Seleccione un Rol --</option> 
                            <?php
                            $roles = $conn->query("SELECT id_rol, nombre_rol FROM rol WHERE estado_rol = 'activo'");
                            while ($r = $roles->fetch_assoc()) {
                                echo "<option value='{$r['id_rol']}'>{$r['nombre_rol']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="margin-top:15px; padding:15px; background:#fff3cd; border:1px solid #ffeeba; border-radius:4px;">
                        <h5 class="w3-text-amber" style="margin-top:0">Cambio de Contraseña</h5>
                        <p style="font-size:0.8rem; margin:0 0 10px 0;">Solo llene estos campos si desea cambiar la clave del usuario.</p>
                        
                        <label style="font-size:0.9rem;">Nueva Contraseña:</label>
                        <input type="password" name="clave_nueva" class="w3-input w3-border" placeholder="Nueva clave">
                        
                        <label style="font-size:0.9rem; margin-top:5px; display:block;">Confirmar Nueva Contraseña:</label>
                        <input type="password" name="clave_confirm" class="w3-input w3-border" placeholder="Repita la nueva clave">
                    </div>

                    <div style="margin-top:15px; padding:10px; background:#e8f0fe; border:1px solid #b3d7ff; border-radius:4px;">
                        <label style="font-weight:bold; color:#003366;">
                            Autorización Requerida:
                        </label>
                        <p style="font-size:0.85rem; margin:0 0 5px 0;">Para guardar cambios, ingrese <b>SU</b> contraseña Actual.</p>
                        <input type="password" name="admin_password" class="w3-input w3-border" placeholder="Su contraseña actual" required>
                    </div>

                    <div class="modal-footer" style="margin-top:20px;">
                        <button type="button" onclick="document.getElementById('modalEditarUs').style.display='none'" 
                                style="background-color: #6c757d; color: white;">Cancelar</button>
                        <button type="submit" style="background-color: #003366; color: white;">
                            Confirmar y Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>