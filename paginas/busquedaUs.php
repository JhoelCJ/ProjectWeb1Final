<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

if (!tienePermiso('ver_usuarios') && $_SESSION['rol'] != 'Administrador') {
    echo "<div class='w3-panel w3-red'>Acceso Denegado</div>"; exit;
}
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1200px; margin:auto;">
        
        <h2 style="border-bottom: 2px solid #003366; color: #003366;">
            Buscar Usuario
        </h2>

        <div style="margin-bottom: 20px; background: #f1f3f5; padding: 20px; border-radius: 4px;">
            <form id="formBusqueda" onsubmit="window.buscarUsuariosAjax(event)">
                <label style="font-weight:bold; color:#003366;">Ingrese nombre, apellido o cédula:</label>
                <div style="display:flex; gap:10px; margin-top:5px;">
                    <input type="text" name="criterio" id="inputCriterio" class="w3-input w3-border" 
                           placeholder="Escriba aquí..." required style="flex:1;">
                    <button type="submit" class="w3-button w3-blue w3-round" style="background-color:#003366!important">
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <div style="overflow-x:auto;">
            <table class="w3-table-all">
                <thead>
                    <tr class="w3-light-grey">
                        <th>Cédula</th>
                        <th>Nombre Completo</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tablaResultados">
                    <tr>
                        <td colspan="6" class="w3-center w3-text-grey" style="padding:20px;">
                            Ingrese un criterio para comenzar la búsqueda.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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
                            <?php
                            $roles = $conn->query("SELECT id_rol, nombre_rol FROM rol WHERE estado_rol = 'activo'");
                            while ($r = $roles->fetch_assoc()) {
                                echo "<option value='{$r['id_rol']}'>{$r['nombre_rol']}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div style="margin-top:15px; padding:10px; background:#f8f9fa; border:1px solid #ddd; border-radius:4px;">
                        <label style="font-size:0.9rem; color:#666;">Cambiar Contraseña (Opcional):</label>
                        <input type="password" name="clave_nueva" class="w3-input w3-border" placeholder="Dejar vacío para no cambiar">
                    </div>

                    <div class="modal-footer" style="margin-top:20px;">
                        <button type="button" onclick="document.getElementById('modalEditarUs').style.display='none'" 
                                style="background-color: #6c757d; color: white;">Cancelar</button>
                        <button type="submit" style="background-color: #003366; color: white;">
                            Actualizar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>