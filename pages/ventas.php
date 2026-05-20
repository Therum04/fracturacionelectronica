<?php include_once("template/cabecera.php"); ?>
<main class="flex-2 p-5 w-full">
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Ventas</h1>
                <p class="text-sm text-gray-500">Gestión de facturas y comprobantes</p>
            </div>
            <div class="flex flex-col md:flex-row gap-3 w-full lg:w-auto">
                <div class="md:w-64">
                    <input type="text" id="q" placeholder="Buscar factura..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="button" onclick="load(1);" class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <a href="factura.php" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                    <i class="fas fa-plus-circle"></i> Nueva Factura
                </a>
            </div>
        </div>
    </div>

    <div id="loader"></div>
    <div class='outer_div'></div>
</main>

<!-- Modal Ver Factura -->
<div id="viewModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-4xl mx-4 max-h-screen overflow-y-auto">
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h4 class="text-lg font-semibold text-gray-800" id="viewModalTitle">Detalle de Factura</h4>
            <button type="button" class="text-gray-500 hover:text-red-500 text-2xl" onclick="cerrarView()">&times;</button>
        </div>
        <div class="p-6">
            <div id="viewContent" class="grid grid-cols-1 gap-4">
            </div>
        </div>
        <div class="flex justify-end border-t px-6 py-3 bg-gray-50">
            <button type="button" onclick="cerrarView()" class="px-4 py-2 bg-gray-200 rounded">Cerrar</button>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <form id="delete_form">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">¿Anular esta factura?</h2>
            <input type="hidden" name="cid" id="delete_cid">
            <input type="hidden" name="eliminar_registro" value="1">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700">Motivo de anulación</label>
                <textarea name="motivo_anulacion" id="motivo_anulacion" rows="2" class="mt-1 w-full border border-gray-300 rounded px-3 py-2" placeholder="Opcional"></textarea>
            </div>
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarDelete()" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Anular</button>
            </div>
        </div>
    </form>
</div>

<?php include_once("template/pie.php"); ?>
<script src="./js/ventas.js"></script>
