<?php include_once("template/cabecera.php"); ?>
<main class="flex-2 p-5 w-full">
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>
                <p class="text-sm text-gray-500">Gestión de clientes del sistema</p>
            </div>
            <div class="flex flex-col md:flex-row gap-3 w-full lg:w-auto">
                <div class="md:w-64">
                    <input type="text" id="q" placeholder="Buscar clientes..." class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
                </div>
                <button type="button" onclick="load(1);" class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                    <i class="fas fa-search"></i> Buscar
                </button>
                <button type="button" onclick="abrirModal();" class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                    <i class="fas fa-plus-circle"></i> Agregar
                </button>
            </div>
        </div>
    </div>

    <div id="loader"></div>
    <div class='outer_div'></div>
</main>

<!-- Modal Registro -->
<div id="clienteModal" class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h4 class="text-lg font-semibold text-gray-800">Registro de Cliente</h4>
            <button type="button" class="text-gray-500 hover:text-red-500 text-2xl" onclick="cerrarModal()">&times;</button>
        </div>
        <div class="p-6">
            <form id="form_cliente" class="grid grid-cols-1 gap-4">
                <input type="hidden" name="id_cliente" id="id_cliente" value="0">
                <input type="hidden" name="add_update" value="1">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Tipo Documento <span class="text-red-500">*</span></label>
                    <select name="id_tipo_doc" id="id_tipo_doc" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2 tipoDocumento_list"></select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Número Documento <span class="text-red-500">*</span></label>
                    <input type="text" name="numero_doc" id="numero_doc" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Razón Social <span class="text-red-500">*</span></label>
                    <input type="text" name="nombre_razon_social" id="nombre_razon_social" required class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Dirección</label>
                    <input type="text" name="direccion" id="direccion" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Correo</label>
                    <input type="email" name="correo" id="correo" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="mt-1 w-full border border-gray-300 rounded px-3 py-2">
                </div>
            </form>
        </div>
        <div class="flex justify-between items-center border-t px-6 py-3 bg-gray-50">
            <button type="button" onclick="cerrarModal()" class="px-4 py-2 bg-gray-200 rounded">Cerrar</button>
            <button type="button" class="btn-guardar bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded shadow">Guardar</button>
        </div>
    </div>
</div>

<!-- Modal Eliminar -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <form id="delete_form">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">¿Eliminar registro?</h2>
            <input type="hidden" name="cid" id="delete_cid">
            <input type="hidden" name="eliminar_registro" value="1">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cerrarDelete()" class="bg-gray-300 px-4 py-2 rounded">Cancelar</button>
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">Eliminar</button>
            </div>
        </div>
    </form>
</div>

<?php include_once("template/pie.php"); ?>
<script src="./js/clientes.js"></script>