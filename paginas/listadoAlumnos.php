<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

if ($_SESSION['rol'] != 'Administrador' && !tienePermiso('gestionar_alumnos')) {
    echo "<div class='w3-panel w3-red'><h3>Acceso Denegado</h3></div>";
    exit;
}

$registrosPorPagina = 6; 
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $registrosPorPagina;

$sqlTotal = "SELECT COUNT(*) as total FROM alumnos";
$resTotal = $conn->query($sqlTotal);
$totalRegistros = $resTotal->fetch_assoc()['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT * FROM alumnos ORDER BY apellido ASC LIMIT $offset, $registrosPorPagina";
$res = $conn->query($sql);
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1200px; margin:auto;">
        
        <h2 style="border-bottom: 2px solid #003366; color: #003366; display:flex; justify-content:space-between;">
            <span>Directorio de Alumnos</span>
            <button onclick="cargarTab('paginas/alumno.php')" class="w3-button w3-blue w3-small w3-round">
                Nuevo Alumno
            </button>
            <button onclick="window.imprimirReporteRapido('alumnos')" class="w3-button w3-red w3-small w3-round" title="Exportar a PDF">
                    Generar Reporte
            </button>
        </h2>

        <div style="overflow-x:auto;">
            <table class="w3-table-all">
                <thead>
                    <tr class="w3-light-grey">
                        <th>Cédula</th>
                        <th>Apellidos y Nombres</th>
                        <th>Contacto</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($res && $res->num_rows > 0) {
                    while ($f = $res->fetch_assoc()) {
                        $id = $f['id_alumno'];
                        $nom = htmlspecialchars($f['nombre'], ENT_QUOTES);
                        $ape = htmlspecialchars($f['apellido'], ENT_QUOTES);
                        $mail = htmlspecialchars($f['email'], ENT_QUOTES);
                        $tel = htmlspecialchars($f['telefono'], ENT_QUOTES);
                        $dir = htmlspecialchars($f['direccion'], ENT_QUOTES);
                        
                        $estado = $f['estado'];
                        $color = ($estado == 'activo') ? 'green' : 'red';

                        echo "<tr>
                            <td>{$f['cedula']}</td>
                            <td><b>$ape $nom</b></td>
                            <td class='w3-small'>
                                $mail<br>
                                $tel
                            </td>
                            <td style='color:$color; font-weight:bold;'>".strtoupper($estado)."</td>
                            <td>
                                <button class='w3-button w3-blue w3-tiny w3-round' title='Editar'
                                    onclick='window.abrirModalEditarAlumno($id, \"$nom\", \"$ape\", \"$mail\", \"$tel\", \"$dir\")'>
                                    Editar
                                </button>";
                        if($estado == 'activo') {
                            echo "<button class='w3-button w3-red w3-tiny w3-round' title='Desactivar' style='margin-left:5px;'
                                    onclick='window.cambiarEstadoAlumno($id, \"desactivar\")'>
                                    Deesactivar
                                  </button>";
                        } else {
                            echo "<button class='w3-button w3-green w3-tiny w3-round' title='Activar' style='margin-left:5px;'
                                    onclick='window.cambiarEstadoAlumno($id, \"activar\")'>
                                    Activar
                                  </button>";
                        }
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='5' class='w3-center'>No hay alumnos registrados.</td></tr>";
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
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/listadoAlumnos.php?pagina=$pagAnt')\">&laquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&laquo;</button>";
                }

                $rango = 2;
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    if ($i == 1 || $i == $totalPaginas || ($i >= $pagina - $rango && $i <= $pagina + $rango)) {
                        $clase = ($i == $pagina) ? 'w3-blue' : 'w3-white';
                        echo "<button class='w3-bar-item w3-button $clase' onclick=\"cargarTab('paginas/listadoAlumnos.php?pagina=$i')\">$i</button>";
                    } elseif ($i == $pagina - $rango - 1 || $i == $pagina + $rango + 1) {
                        echo "<span class='w3-bar-item w3-button w3-disabled'>...</span>";
                    }
                }

                if ($pagina < $totalPaginas) {
                    $pagSig = $pagina + 1;
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/listadoAlumnos.php?pagina=$pagSig')\">&raquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&raquo;</button>";
                }
                ?>
            </div>
            <div class="w3-tiny w3-text-grey w3-margin-top">
                Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?> (Total: <?php echo $totalRegistros; ?>)
            </div>
        </div>
        <?php } ?>

    </div>

    <div id="modalEditarAlumno" class="modal-overlay">
        <div class="modal-content-corp">
            <div class="modal-header">
                <h3>Editar Datos del Alumno</h3>
                <span class="close-btn" onclick="document.getElementById('modalEditarAlumno').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <form onsubmit="window.editarAlumnoAdminAjax(event)">
                    <input type="hidden" name="id_alumno" id="ea_id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nombre:</label>
                            <input type="text" name="nombre" id="ea_nombre" class="w3-input w3-border" required>
                        </div>
                        <div class="form-group">
                            <label>Apellido:</label>
                            <input type="text" name="apellido" id="ea_apellido" class="w3-input w3-border" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Institucional:</label>
                        <input type="email" name="email" id="ea_email" class="w3-input w3-border" readonly>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label>Teléfono:</label>
                            <input type="text" name="telefono" id="ea_telefono" class="w3-input w3-border">
                        </div>
                        <div class="form-group">
                            <label>Dirección:</label>
                            <input type="text" name="direccion" id="ea_direccion" class="w3-input w3-border">
                        </div>
                    </div>

                    <div class="modal-footer" style="margin-top:20px;">
                        <button type="button" onclick="document.getElementById('modalEditarAlumno').style.display='none'" 
                                style="background-color: #6c757d; color: white;">Cancelar</button>
                        <button type="submit" style="background-color: #003366; color: white;">
                            Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>