<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");
exigirPermiso('gestionar_cursos');

$registrosPorPagina = 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $registrosPorPagina;

$sqlTotal = "SELECT COUNT(*) as total FROM cursos";
$resTotal = $conn->query($sqlTotal);
$rowTotal = $resTotal->fetch_assoc();
$totalRegistros = $rowTotal['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$res = $conn->query("SELECT * FROM cursos ORDER BY id_curso DESC LIMIT $offset, $registrosPorPagina");
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1100px; margin:auto;">
        
        <h2 style="border-bottom: 2px solid #003366; color: #003366; display:flex; justify-content:space-between;">
            <span>Gestión de Cursos</span>
            <button onclick="window.abrirModalCurso()" class="w3-button w3-blue w3-small w3-round">
                Nuevo Curso
            </button>
            <button onclick="window.imprimirReporteRapido('cursos')" class="w3-button w3-orange w3-text-white w3-small w3-round" title="Exportar Cursos">
                    Generar reporte
            </button>
        </h2>

        <div style="overflow-x:auto;">
            <table class="w3-table-all">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Instructor</th>
                        <th>Especialidad</th>
                        <th>Cupo</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if ($res && $res->num_rows > 0) {
                    while ($c = $res->fetch_assoc()) {
                        $id = $c['id_curso'];
                        $nom = htmlspecialchars($c['nombre_curso']);
                        $cap = $c['capacidad'];
                        $inst = htmlspecialchars($c['instructor']);
                        $esp = htmlspecialchars($c['especialidad']);
                        $estado = $c['estado_curso'];
                        $color = ($estado == 'activo') ? 'green' : 'red';

                        echo "<tr>
                            <td><b>$nom</b></td>
                            <td>$inst</td>
                            <td>$esp</td>
                            <td>$cap</td>
                            <td style='color:$color; font-weight:bold;'>".strtoupper($estado)."</td>
                            <td>
                                <button class='w3-button w3-blue w3-tiny w3-round' title='Editar'
                                    onclick='window.abrirModalCurso($id, \"$nom\", \"$cap\", \"$inst\", \"$esp\")'>
                                    Editar
                                </button>";
                                
                        if($estado == 'activo') {
                            echo "<button class='w3-button w3-red w3-tiny w3-round' title='Desactivar' style='margin-left:5px;'
                                    onclick='window.cambiarEstadoCurso($id, \"desactivar\")'>
                                    Desactivar
                                  </button>";
                        } else {
                            echo "<button class='w3-button w3-green w3-tiny w3-round' title='Activar' style='margin-left:5px;'
                                    onclick='window.cambiarEstadoCurso($id, \"activar\")'>
                                    Activar
                                  </button>";
                        }
                        echo "</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='w3-center'>No hay cursos registrados.</td></tr>";
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
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/visualizacionCursos.php?pagina=$pagAnt')\">&laquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&laquo;</button>";
                }

                $rango = 2;
                for ($i = 1; $i <= $totalPaginas; $i++) {
                    if ($i == 1 || $i == $totalPaginas || ($i >= $pagina - $rango && $i <= $pagina + $rango)) {
                        $claseActiva = ($i == $pagina) ? 'w3-blue' : 'w3-white';
                        echo "<button class='w3-bar-item w3-button $claseActiva' onclick=\"cargarTab('paginas/visualizacionCursos.php?pagina=$i')\">$i</button>";
                    } elseif ($i == $pagina - $rango - 1 || $i == $pagina + $rango + 1) {
                        echo "<span class='w3-bar-item w3-button w3-disabled'>...</span>";
                    }
                }

                if ($pagina < $totalPaginas) {
                    $pagSig = $pagina + 1;
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/visualizacionCursos.php?pagina=$pagSig')\">&raquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&raquo;</button>";
                }
                ?>
            </div>
            <div class="w3-tiny w3-text-grey w3-margin-top">
                Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?> (Total: <?php echo $totalRegistros; ?> cursos)
            </div>
        </div>
        <?php } ?>

    </div>

    <div id="modalCurso" class="modal-overlay">
        <div class="modal-content-corp">
            <div class="modal-header">
                <h3 id="tituloModalCurso">Nuevo Curso</h3>
                <span class="close-btn" onclick="document.getElementById('modalCurso').style.display='none'">&times;</span>
            </div>
            <div class="modal-body">
                <form action="php/curso/guardar_curso.php" method="POST" onsubmit="window.guardarCursoAjax(event)">
                    <input type="hidden" name="id_curso" id="c_id_curso">
                    <div class="form-group">
                        <label>Nombre del Curso:</label>
                        <input type="text" name="nombre_curso" id="c_nombre" class="w3-input w3-border" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Instructor:</label>
                            <input type="text" name="instructor" id="c_instructor" class="w3-input w3-border">
                        </div>
                        <div class="form-group">
                            <label>Especialidad:</label>
                            <input type="text" name="especialidad" id="c_especialidad" class="w3-input w3-border">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Capacidad (Cupos):</label>
                        <input type="number" name="capacidad" id="c_capacidad" class="w3-input w3-border" required>
                    </div>
                    <div class="modal-footer" style="margin-top:20px;">
                        <button type="button" onclick="document.getElementById('modalCurso').style.display='none'" 
                                style="background-color: #6c757d; color: white;">Cancelar</button>
                        <button type="submit" style="background-color: #003366; color: white;">
                            Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>