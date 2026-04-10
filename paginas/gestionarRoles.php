<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

exigirPermiso('gestionar_roles');
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    
    <div class="contenedor" style="max-width:800px; margin:auto;">
        <h2>Gestión de Roles y Permisos</h2>
        
        <form method="POST" action="php/roles/guardar_rol.php" onsubmit="window.guardarRolAjax(event)">
            <input type="hidden" name="id_rol_edit" id="id_rol_edit">
            
            <label>Nombre del Rol</label>
            <input type="text" name="nombre_rol" id="nombre_rol" required>
            
            <label>Descripción</label>
            <input type="text" name="descripcion_rol" id="descripcion_rol" required>

            <?php if (tienePermiso('asignar_permisos')) { ?>
            <div class="w3-margin-top">
                <label><b>Asignar Permisos:</b></label><br>
                <div class="w3-row">
                    <?php
                    $resP = $conn->query("SELECT * FROM permisos");
                    if ($resP) {
                        while ($p = $resP->fetch_assoc()) {
                            echo "<div class='w3-col s6'>
                                      <input type='checkbox' name='permisos[]' value='{$p['id_permiso']}' id='perm_{$p['id_permiso']}'> 
                                      <label for='perm_{$p['id_permiso']}'>{$p['descripcion']}</label>
                                    </div>";
                        }
                    }
                    ?>
                </div>
            </div>
            <?php } ?>
            
            <div style="margin-top: 20px;">
                <input type="submit" value="Guardar Rol">
                <button type="button" onclick="window.limpiarFormRol()" style="background-color: #6c757d; color: white; padding: 10px 25px; border:none; border-radius:4px; cursor:pointer;">Limpiar</button>
            </div>

            <div id="area_estado_rol" style="display:none; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ccc;">
                <label>Estado del Rol:</label>
                <button type="button" id="btn_activar_rol" onclick="window.cambiarEstadoRol('activar')" 
                        style="background-color: #28a745; color: white; padding: 8px 15px; border:none; border-radius:4px; cursor:pointer; display:none;">
                    Activar Rol
                </button>
                
                <button type="button" id="btn_desactivar_rol" onclick="window.cambiarEstadoRol('desactivar')" 
                        style="background-color: #dc3545; color: white; padding: 8px 15px; border:none; border-radius:4px; cursor:pointer; display:none;">
                    Desactivar Rol
                </button>
                <p style="font-size: 0.8rem; color: #666; margin-top:5px;">
                    <i>Nota: Si desactiva un rol, los usuarios con ese rol perderán acceso al sistema.</i>
                </p>
            </div>
        </form>
    </div>

    <div class="contenedor" style="max-width:800px; margin: 30px auto;">
        <h3>Roles Existentes</h3>
        <ul class="w3-ul w3-border">
            <?php
            $roles = $conn->query("SELECT * FROM rol");
            if ($roles && $roles->num_rows > 0) {
                while ($r = $roles->fetch_assoc()) {
                    $perms = [];
                    $rp = $conn->query("SELECT id_permiso FROM rol_permisos WHERE id_rol=".$r['id_rol']);
                    if ($rp) {
                        while($row = $rp->fetch_assoc()) $perms[] = $row['id_permiso'];
                    }
                    $jsonPerms = json_encode($perms);
                    
                    $nombreSafe = htmlspecialchars($r['nombre_rol'], ENT_QUOTES);
                    $descSafe = htmlspecialchars($r['descripcion_rol'], ENT_QUOTES);
                    $estado = isset($r['estado_rol']) ? $r['estado_rol'] : 'activo';
                    $colorEstado = ($estado == 'activo') ? 'green' : 'red';

                    $esRolProtegido = ($r['id_rol'] == 1);

                    echo "<li class='w3-display-container' style='border-left: 5px solid $colorEstado; padding-left: 15px;'>
                            <b>{$r['nombre_rol']}</b> <span style='font-size:0.8em; color:$colorEstado'>(".strtoupper($estado).")</span>
                            <br><span style='font-size:0.9em; color:#666'>{$r['descripcion_rol']}</span>";

                    if (tienePermiso('gestionar_roles')) {
                        if ($esRolProtegido) {
                            echo "<span class='w3-display-right w3-tag w3-black w3-small' style='border-radius:4px; margin-right:10px;'>
                                    No editable
                                  </span>";
                        } else {
                            echo "<span onclick='window.editarRol({$r['id_rol']}, \"$nombreSafe\", \"$descSafe\", $jsonPerms, \"$estado\")' 
                                  class='w3-button w3-blue w3-display-right w3-small' style='border-radius:4px;'>Editar</span>";
                        }
                    }
                    echo "</li>";
                }
            } else {
                echo "<li class='w3-padding'>No hay roles registrados.</li>";
            }
            ?>
        </ul>
    </div>

</div>