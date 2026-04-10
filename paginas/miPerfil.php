<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");

$idUser = $_SESSION['id_user'];
$sql = "SELECT u.*, r.nombre_rol, r.descripcion_rol 
        FROM usuarios u 
        INNER JOIN rol r ON u.id_rol = r.id_rol 
        WHERE u.id_user = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idUser);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $datos = $res->fetch_assoc();
} else {
    echo "Error al cargar datos.";
    exit;
}
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    
    <div class="contenedor" style="max-width:700px; margin:auto;">
        
        <h2 style="border-bottom: 2px solid #003366; color: #003366; padding-bottom: 10px;">
            Ficha de Usuario
        </h2>

        <div class="w3-row-padding" style="margin-top: 20px;">
            
            <div class="w3-col m4 text-center">
                <div style="background: #f1f3f5; padding: 20px; border-radius: 8px; border: 1px solid #ddd;">
                    <i class="fa fa-user-circle-o" style="font-size: 80px; color: #003366;"></i>
                    <h4 style="margin-top: 15px; font-weight: bold; color: #333;">
                        <?php echo htmlspecialchars($datos['usuario_user']); ?>
                    </h4>
                    <span style="background: #003366; color: white; padding: 3px 10px; border-radius: 15px; font-size: 0.8rem;">
                        <?php echo htmlspecialchars($datos['nombre_rol']); ?>
                    </span>
                </div>
            </div>

            <div class="w3-col m8">
                <table class="w3-table w3-bordered" style="font-size: 0.95rem;">
                    <tr>
                        <td style="width: 150px; font-weight: bold; color: #555;">Nombre Completo:</td>
                        <td>
                            <?php echo htmlspecialchars($datos['nombre_user'] . " " . $datos['apellido_user']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #555;">Cédula / ID:</td>
                        <td>
                            <?php echo htmlspecialchars($datos['cedula_user']); ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #555;">Estado Cuenta:</td>
                        <td>
                            <?php if ($datos['estado_user'] == 'activo') { ?>
                                <span style="color: green; font-weight: bold;">Activo</span>
                            <?php } else { ?>
                                <span style="color: red; font-weight: bold;">Inactivo</span>
                            <?php } ?>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #555;">Fecha de Nacimiento:</td>
                        <td>
                            <?php echo date("d/m/Y", strtotime($datos['fecha_nacimiento'])); ?>
                        </td>
                    </tr>
                </table>

                <div class="w3-panel w3-leftbar w3-light-grey w3-border-blue" style="margin-top: 20px; padding: 15px;">
                    <p style="font-size: 0.9rem; margin: 0;">
                        <b>Información del Sistema:</b><br>
                        Su rol actual tiene la siguiente descripción: 
                        <i>"<?php echo htmlspecialchars($datos['descripcion_rol']); ?>"</i>.
                        <br>Si necesita actualizar sus datos, contacte al departamento de TI.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>