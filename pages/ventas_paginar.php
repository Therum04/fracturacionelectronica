<?php
session_start();
include "../admin/classes/Database.php";
$db = new Database();
$con = $db->connect();
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $query = mysqli_real_escape_string($con, trim((strip_tags($_REQUEST['query'], ENT_QUOTES))));

    $tables = "factura_cabecera fc 
    left join cliente c on fc.id_cliente = c.id_cliente
    left join tipo_comprobante tc on fc.id_tipo_comp = tc.id_tipo_comp
    left join moneda m on fc.id_moneda = m.id_moneda
    left join tipo_metodo_pago tm on fc.id_metodo_pago = tm.id_metodo_pago
    left join tipo_estado te on fc.id_estado = te.id_estado
    left join empresa e on fc.id_empresa = e.id_empresa";
    $campos = " fc.*, c.nombre_razon_social as cliente_nombre, c.numero_doc as cliente_doc, 
                tc.nombre as tipo_comprobante_nombre, m.nombre as moneda_nombre, 
                tm.nombre as metodo_pago_nombre, te.nombre as estado_nombre,
                e.nombre_razon_social as empresa_nombre ";
    $sWhere = " (fc.serie LIKE '%" . $query . "%' OR fc.numero_factura LIKE '%" . $query . "%' 
                OR c.nombre_razon_social LIKE '%" . $query . "%' 
                OR c.numero_doc LIKE '%" . $query . "%')";
    $sWhere .= " ORDER BY fc.id_factura desc";

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
            <th class="px-4 py-2 text-left font-semibold uppercase">Fecha</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Cliente</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Doc. Cliente</th>
            <th class="px-4 py-2 text-left font-semibold uppercase">Moneda</th>
            <th class="px-4 py-2 text-right font-semibold uppercase">Total</th>
            <th class="px-4 py-2 text-center font-semibold uppercase">Estado</th>
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
              <td class="px-4 py-2"><?php echo $row['serie'] . '-' . $row['numero_factura']; ?></td>
              <td class="px-4 py-2"><?php echo $row['fecha_emision']; ?></td>
              <td class="px-4 py-2"><?php echo $row['cliente_nombre']; ?></td>
              <td class="px-4 py-2"><?php echo $row['cliente_doc']; ?></td>
              <td class="px-4 py-2"><?php echo $row['moneda_nombre']; ?></td>
              <td class="px-4 py-2 text-right"><?php echo number_format($row['total'], 2); ?></td>
              <td class="px-4 py-2 text-center">
                <?php if ($row['id_estado'] == 1) { ?>
                  <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs">● <?php echo $row['estado_nombre']; ?></span>
                <?php } else { ?>
                  <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs">● <?php echo $row['estado_nombre']; ?></span>
                <?php } ?>
              </td>
              <td class="px-4 py-2 text-center flex justify-center gap-2">
                  <a href="#" class="bg-sky-500 hover:bg-sky-600 text-white px-2 py-1 rounded text-xs view-registro" title="Ver" data-id="<?php echo $row['id_factura']; ?>">
                    <i class="fas fa-eye"></i>
                  </a>
                  <?php if ($row['id_estado'] == 1) { ?>
                  <a href="#" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs delete-registro" title="Anular" data-cid="<?php echo $row['id_factura']; ?>">
                    <i class="fas fa-trash"></i>
                  </a>
                  <?php } ?>
              </td>
            </tr>
          <?php } ?>
          <tr class="bg-gray-50 text-sm text-gray-600">
            <td colspan="9" class="px-4 py-3">
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
        echo '<div class="bg-white p-6 rounded-lg shadow text-center text-gray-500">No se encontraron facturas</div>';
    }
}
?>
