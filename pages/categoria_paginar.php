<?php
session_start();
include "../admin/classes/Database.php";
$db = new Database();
$con = $db->connect();
$action = (isset($_REQUEST['action']) && $_REQUEST['action'] != NULL) ? $_REQUEST['action'] : '';
if ($action == 'ajax') {
    $query = mysqli_real_escape_string($con, trim(strip_tags($_REQUEST['query'])));

    $tables = "categorias c";
    $campos = " c.* ";
    $sWhere = " (c.nombre LIKE '%" . $query . "%')";
    $sWhere .= " ORDER BY c.id DESC";
    include 'pagination.php';
    $page = (isset($_REQUEST['page']) && !empty($_REQUEST['page'])) ? $_REQUEST['page'] : 1;
    $per_page = intval($_REQUEST['per_page']);
    $adjacents  = 4;
    $offset = ($page - 1) * $per_page;
    $count_query = mysqli_query($con, "SELECT count(*) AS numrows FROM $tables WHERE $sWhere");

    if ($row = mysqli_fetch_array($count_query)) {
        $numrows = $row['numrows'];
    } else {
        echo mysqli_error($con);
    }
    $total_pages = ceil($numrows / $per_page);
    $query = mysqli_query($con, "SELECT $campos FROM $tables WHERE $sWhere LIMIT $offset,$per_page");

    if ($numrows > 0) {
?>

        <div class="overflow-x-auto bg-white shadow rounded-lg">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-[rgb(20,184,166)] text-white text-sm">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold uppercase">N°</th>
                        <th class="px-4 py-2 text-left font-semibold uppercase">Nombre</th>
                        <th class="px-4 py-2 text-left font-semibold uppercase">Descripción</th>
                        <th class="px-4 py-2 text-left font-semibold uppercase">Estado</th>
                        <th class="px-4 py-2 text-center font-semibold uppercase">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 text-sm text-gray-700">
                    <?php
                    $finales = 0;
                    while ($row = mysqli_fetch_array($query)) {
                        $id = $row['id'];
                        $nombre = $row['nombre'];
                        $descripcion = $row['descripcion'];
                        $estado = $row['estado'];

                        $finales++;
                    ?>
                        <tr>
                            <td class="px-4 py-2"><?php echo $finales; ?></td>
                            <td class="px-4 py-2"><?php echo $nombre; ?></td>
                            <td class="px-4 py-2"><?php echo $descripcion; ?></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-1 text-xs font-semibold rounded <?php echo $estado == 'ACTIVO' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                                    <?php echo $estado; ?>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center flex justify-center gap-2">
                                <a href="#" class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded text-xs edit-registro" data-id="<?php echo $id; ?>" title="Modificar">
                                    <span class="hidden"><?php echo htmlspecialchars(json_encode($row)); ?></span>
                                    ✏️
                                </a>
                                <a href="#" class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs delete-registro" data-id="<?php echo $id; ?>" title="Eliminar">
                                    🗑️
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr class="bg-gray-50 text-sm text-gray-600">
                        <td colspan="5" class="px-4 py-3">
                            <?php
                            $inicios = $offset + 1;
                            $finales += $inicios - 1;
                            echo "Mostrando $inicios al $finales de $numrows registros";
                            echo paginate($page, $total_pages, $adjacents);
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

<?php
    }
}
?>
