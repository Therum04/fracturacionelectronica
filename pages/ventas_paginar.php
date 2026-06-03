<?php
session_start();
include "../admin/classes/Database.php";
$db = new Database();
$con = $db->connect();
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $query = mysqli_real_escape_string($con, trim((strip_tags($_REQUEST['query'], ENT_QUOTES))));

    $tables = "ventas v
    left join clientes c on v.id_cliente = c.id";
    $campos = " v.*,
                COALESCE(NULLIF(c.razon_social, ''), TRIM(CONCAT_WS(' ', c.nombres, c.apellido_paterno, c.apellido_materno))) as cliente_nombre,
                c.numero_documento as cliente_doc ";
    $sWhere = " (v.serie LIKE '%" . $query . "%' OR v.correlativo LIKE '%" . $query . "%'
                OR c.razon_social LIKE '%" . $query . "%' OR c.nombres LIKE '%" . $query . "%'
                OR c.numero_documento LIKE '%" . $query . "%')";
    $sWhere .= " ORDER BY v.id desc";

    include 'pagination.php';
    $page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page = intval($_REQUEST['per_page']);
    $adjacents = 4;
    $offset = ($page - 1) * $per_page;

    $count_query = mysqli_query($con, "SELECT count(*) AS numrows FROM $tables where $sWhere");
    $row = mysqli_fetch_array($count_query);
    $numrows = $row['numrows'];
    $total_pages = ceil($numrows / $per_page);
    $sql = "SELECT $campos FROM $tables where $sWhere LIMIT $offset,$per_page";
    $query = mysqli_query($con, $sql);

    if ($numrows > 0) {
?>
    <div class="overflow-x-auto bg-white shadow rounded-lg">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-teal-600 text-white text-sm">
          <tr>
            <th class="px-4 py-2 text-left font-semibold uppercase">N°</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Comprobante</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Tipo</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Fecha</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Cliente</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Doc. Cliente</th>
            <th class="px-4 py-2 text-right font-semibold uppercase">Total</th>
            <th class="px-4 py-2 text-center font-semibold uppercase">Estado</th>
            <th class="px-4 py-2 text-center font-semibold uppercase">SUNAT</th>
            <th class="px-4 py-2 text-center font-semibold uppercase">Acción</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
          <?php
          $i = $offset + 1;
          while ($row = mysqli_fetch_array($query)) {
          ?>
            <tr>
              <td class="px-4 py-2"><?php echo $i++; ?></td>
              <td class="px-4 py-2 font-medium"><?php echo $row['serie'] . '-' . str_pad($row['correlativo'], 6, '0', STR_PAD_LEFT); ?></td>
              <td class="px-4 py-2"><?php echo $row['tipo_comprobante']; ?></td>
              <td class="px-4 py-2"><?php echo date('d/m/Y', strtotime($row['fecha_emision'])); ?></td>
              <td class="px-4 py-2"><?php echo $row['cliente_nombre']; ?></td>
              <td class="px-4 py-2"><?php echo $row['cliente_doc']; ?></td>
              <td class="px-4 py-2 text-right">S/ <?php echo number_format($row['total'], 2); ?></td>
              <td class="px-4 py-2 text-center">
                <?php if ($row['estado'] == 'COMPLETADO') { ?>
                  <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs">● COMPLETADO</span>
                <?php } else { ?>
                  <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs">● ANULADO</span>
                <?php } ?>
              </td>
              <td class="px-4 py-2 text-center">
                <?php
                $sunatClass = 'bg-gray-100 text-gray-600';
                $sunatText = 'PENDIENTE';
                if ($row['sunat_estado'] == 'aceptado') {
                    $sunatClass = 'bg-green-100 text-green-600';
                    $sunatText = 'ACEPTADO';
                } elseif ($row['sunat_estado'] == 'rechazado') {
                    $sunatClass = 'bg-red-100 text-red-600';
                    $sunatText = 'RECHAZADO';
                } elseif ($row['sunat_estado'] == 'baja') {
                    $sunatClass = 'bg-orange-100 text-orange-600';
                    $sunatText = 'BAJA';
                }
                ?>
                <span class="<?php echo $sunatClass; ?> px-2 py-1 rounded-full text-xs"><?php echo $sunatText; ?></span>
              </td>
              <td class="px-4 py-2 text-center flex justify-center gap-2">
                  <a href="#" class="bg-sky-500 hover:bg-sky-600 text-white px-2 py-1 rounded text-xs view-registro" title="Ver" data-id="<?php echo $row['id']; ?>">
                    <i class="fas fa-eye"></i>
                  </a>
                  <a href="print_venta.php?id=<?php echo $row['id']; ?>&formato=ticket" target="_blank" class="bg-teal-500 hover:bg-teal-600 text-white px-2 py-1 rounded text-xs" title="Imprimir ticket">
                    <i class="fas fa-receipt"></i>
                  </a>
                  <a href="print_venta.php?id=<?php echo $row['id']; ?>&formato=a4" target="_blank" class="bg-purple-500 hover:bg-purple-600 text-white px-2 py-1 rounded text-xs" title="Imprimir A4">
                    <i class="fas fa-file"></i>
                  </a>
                  <?php if ($row['estado'] == 'COMPLETADO') { ?>
                  <a href="#" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs delete-registro" title="Anular" data-id="<?php echo $row['id']; ?>">
                    <i class="fas fa-trash"></i>
                  </a>
                  <?php } ?>
              </td>
            </tr>
          <?php } ?>
          <tr class="bg-gray-50 text-sm text-gray-600">
            <td colspan="10" class="px-4 py-3">
              <?php
              $inicios = $offset + 1;
              $finales = $i - 1;
              echo "Mostrando $inicios al $finales de $numrows registros";
              echo paginate($page, $total_pages, $adjacents);
              ?>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
<?php
    } else {
        echo '<div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">No se encontraron ventas</div>';
    }
}
?>
