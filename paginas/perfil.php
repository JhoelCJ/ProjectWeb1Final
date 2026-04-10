<?php
session_start();
include_once("../php/conexion.php");

if (!isset($_SESSION['tipo_sesion']) || $_SESSION['tipo_sesion'] != 'alumno') { 
    echo "<div class='w3-panel w3-red'>Acceso denegado</div>"; 
    exit; 
}

$id = $_SESSION['id_alumno'];
$res = $conn->query("SELECT * FROM alumnos WHERE id_alumno = $id");
$d = $res->fetch_assoc();
?>

<div class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:600px; margin:auto;">
        <h2 style="color: #003366;">Mi Perfil</h2>

        <form onsubmit="window.actualizarPerfilAlumno(event)">
            <input type="hidden" name="id_alumno" value="<?php echo $d['id_alumno']; ?>">
            
            <label>Usuario:</label>
            <input type="text" class="w3-input w3-border w3-light-grey" value="<?php echo $d['usuario']; ?>" readonly>
            
            <label>Email Institucional:</label>
            <input type="email" name="email" class="w3-input w3-border" 
                   value="<?php echo $d['email']; ?>" 
                   readonly>
            <small class="w3-text-grey">Debe terminar en @espe.edu.ec</small>
            
            <br><br>
            <label>Dirección:</label>
            <input type="text" name="direccion" class="w3-input w3-border" value="<?php echo $d['direccion']; ?>">

            <label>Teléfono:</label>
            <input type="text" name="telefono" class="w3-input w3-border" value="<?php echo $d['telefono']; ?>">
            
            <p style="font-weight:bold; color:#003366;">Cambio de Contraseña (Opcional)</p>

            <div class="w3-row-padding">
                <div class="w3-half">
                <label>Contraseña Actual:</label>
                    <input type="password" name="clave_actual" class="w3-input w3-border" placeholder="Obligatorio para cambiar clave">
                </div>
                <div class="w3-half">
                <label>Nueva Contraseña:</label>
                    <input type="password" name="clave_nueva" class="w3-input w3-border" placeholder="Dejar vacío si no desea cambiar">
                </div>
            </div>

            <button type="submit" class="w3-button w3-blue w3-block w3-margin-top" style="background-color:#003366!important">
                Actualizar Datos
            </button>
        </form>
    </div>
</div>