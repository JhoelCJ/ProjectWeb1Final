<?php
include_once("../php/validarSesion.php");
include_once("../php/conexion.php");
include_once("../php/verificarPermiso.php");

if ($_SESSION['rol'] != 'Administrador') {
    echo "<div class='w3-panel w3-red'><h3>Acceso Denegado</h3></div>";
    exit;
}


$registrosPorPagina = 10; 
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$offset = ($pagina - 1) * $registrosPorPagina;


$sqlTotal = "SELECT COUNT(*) as total FROM auditoria";
$resTotal = $conn->query($sqlTotal);
$rowTotal = $resTotal->fetch_assoc();
$totalRegistros = $rowTotal['total'];
$totalPaginas = ceil($totalRegistros / $registrosPorPagina);

$sql = "SELECT * FROM auditoria ORDER BY fecha DESC LIMIT $offset, $registrosPorPagina";
$result = $conn->query($sql);
?>

<div id="contenido-spa" class="w3-container w3-padding-32">
    <div class="contenedor" style="max-width:1200px; margin:auto;">
        
        <h2 style="border-bottom: 2px solid #003366; color: #003366;">
            Bitácora de Acciones
            <button onclick="window.imprimirReporteRapido('auditoria')" class="w3-button w3-red w3-small w3-round" title="Exportar Historial">
                Generar Reporte
            </button>
        </h2>

        <style>
            .tabla-auditoria { width: 100%; border-collapse: collapse; margin-top: 20px; }
            .tabla-auditoria th, .tabla-auditoria td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 14px; }
            .tabla-auditoria th { background-color: #f2f2f2; }
            .tabla-auditoria tr:nth-child(even) { background-color: #f9f9f9; }
        </style>

        <div style="overflow-x:auto;">
            <table class="tabla-auditoria">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha</th>
                        <th>Usuario</th>
                        <th>Rol (Histórico)</th>
                        <th>Acción</th>
                        <th>Detalle</th>
                        </tr>
                </thead>
                <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['id_log']}</td>
                                <td>{$row['fecha']}</td>
                                <td><b>{$row['usuario']}</b></td>
                                <td>{$row['rol']}</td>
                                <td><span class='w3-tag w3-blue'>{$row['accion']}</span></td>
                                <td>{$row['detalle']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' class='w3-center'>No hay registros de auditoría.</td></tr>";
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
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/verAuditoria.php?pagina=$pagAnt')\">&laquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&laquo;</button>";
                }

                $rango = 2; 

                for ($i = 1; $i <= $totalPaginas; $i++) {
                    
                    if ($i == 1 || $i == $totalPaginas || ($i >= $pagina - $rango && $i <= $pagina + $rango)) {
                        
                        $claseActiva = ($i == $pagina) ? 'w3-blue' : 'w3-white';
                        echo "<button class='w3-bar-item w3-button $claseActiva' onclick=\"cargarTab('paginas/verAuditoria.php?pagina=$i')\">$i</button>";
                    
                    } elseif ($i == $pagina - $rango - 1 || $i == $pagina + $rango + 1) {
                        echo "<span class='w3-bar-item w3-button w3-disabled'>...</span>";
                    }
                }

                if ($pagina < $totalPaginas) {
                    $pagSig = $pagina + 1;
                    echo "<button class='w3-bar-item w3-button' onclick=\"cargarTab('paginas/verAuditoria.php?pagina=$pagSig')\">&raquo;</button>";
                } else {
                    echo "<button class='w3-bar-item w3-button w3-disabled'>&raquo;</button>";
                }
                ?>
            </div>
            
            <div class="w3-tiny w3-text-grey w3-margin-top">
                Página <?php echo $pagina; ?> de <?php echo $totalPaginas; ?> (Total: <?php echo $totalRegistros; ?> registros)
            </div>
        </div>
        <?php } ?>

    </div>
</div>