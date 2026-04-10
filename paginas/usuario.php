<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

if ($_SESSION['rol'] != 'Administrador') {
    echo "<div class='w3-panel w3-red'><h3>Acceso Denegado</h3><p>No tienes permisos para ver esta sección.</p></div>";
    exit;
}
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    
    <div class="contenedor" style="max-width:600px; margin:auto;">
        <h1>Nuevo Usuario / Alumno</h1>
        
        <form method="post" action="php/usuario/insercion.php" onsubmit="validarYEnviarVertical(event)">
            
            <fieldset>
                <legend>DATOS PERSONALES</legend>
                
                <p>
                    <label>Nombres:</label>
                    <input type="text" name="nombre" class="w3-input w3-border" required>
                </p>
                <p>
                    <label>Apellidos:</label>
                    <input type="text" name="apellido" class="w3-input w3-border" required>
                </p>
                <p>
                    <label>Cédula:</label>
                    <input type="text" name="cedula" id="cedula_input" class="w3-input w3-border" maxlength="10" required>
                </p>
                <p>
                    <label>F. Nacimiento:</label>
                    <input type="date" name="fecha_nacimiento" id="fecha_nacimiento" class="w3-input w3-border" onchange="calcularEdad()" required>
                </p>
                <p>
                    <label>Edad:</label>
                    <input type="text" name="edad" id="edad" class="w3-input w3-border" readonly style="background:#eee;">
                </p>
                <p>
                    <label>Dirección:</label>
                    <input type="text" name="direccion" class="w3-input w3-border">
                </p>
                <p>
                    <label>Teléfono:</label>
                    <input type="text" name="telefono" class="w3-input w3-border" maxlength="10">
                </p>
            </fieldset>

            <fieldset>
                <legend>CUENTA Y ROL</legend>
                <p>
                    <label>Usuario:</label>
                    <input type="text" name="usuario" class="w3-input w3-border" required>
                </p>
                <p>
                    <label>Clave:</label>
                    <input type="password" name="clave" class="w3-input w3-border" required>
                </p>
                <p>
                    <label>Rol:</label>
                    <select name="rol" class="w3-select w3-border">
                        <option value="">-- Seleccione --</option>
                        <?php
                        $resRoles = $conn->query("SELECT id_rol, nombre_rol FROM rol WHERE estado_rol = 'activo'");
                        if ($resRoles) {
                            while($row = $resRoles->fetch_assoc()) {
                                echo "<option value='{$row['id_rol']}'>{$row['nombre_rol']}</option>";
                            }
                        }
                        ?>
                    </select>
                </p>
            </fieldset>

            <div style="margin-top:20px; text-align:center;">
                <input type="submit" value="Registrar" class="w3-button w3-blue">
                <button type="button" onclick="cargarTab('paginas/visualizacionUs.php')" class="w3-button w3-red">Cancelar</button>
            </div>
        </form>
    </div>
</div>
