<?php include_once("template/cabecera.php"); ?>
<main class="flex-2 p-5 w-full">

    <!-- HEADER -->
    <div class="flex items-center justify-between mb-4 md:hidden">
        <button onclick="toggleMenu()" class="text-2xl">☰</button>
        <span class="font-semibold">Usuarios</span>
    </div>



    <!-- HEADER / TOOLBAR -->
    <div class="bg-white p-6 rounded-xl shadow mb-6">

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4">

            <!-- TÍTULO -->
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Usuarios</h1>
                <p class="text-sm text-gray-500">Gestión de usuarios del sistema</p>
            </div>

            <!-- BUSCADOR + BOTONES -->
            <div class="flex flex-col md:flex-row gap-3 w-full lg:w-auto">

                <!-- INPUT -->
                <div class="md:w-64">
                    <input
                        type="text"
                        name="q"
                        id="q"
                        maxlength="50"
                        placeholder="Buscar usuario..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900
                 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- BOTÓN BUSCAR -->
                <button
                    type="button"
                    onclick="load(1);"
                    class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg
               text-sm flex items-center justify-center gap-2 transition shadow">
                    <i class="fas fa-search"></i>
                    Buscar
                </button>

                <!-- BOTÓN AGREGAR -->
                <button
                    type="button"
                    onclick="abrirUsuario();"
                    class="bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg
               text-sm flex items-center justify-center gap-2 transition shadow">
                    <i class="fas fa-plus-circle"></i>
                    Agregar
                </button>

            </div>

        </div>
    </div>


    <div class="col-md-12">
        <div id="loader"></div><!-- Carga de datos ajax aqui -->
        <div id="resultados"></div><!-- Carga de datos ajax aqui -->
        <div class='outer_div'></div><!-- Carga de datos ajax aqui -->
    </div>


</main>

<div id="usuarioModal"
    class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">

    <!-- Caja modal -->
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">

        <!-- Header -->
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h4 class="text-lg font-semibold text-gray-800">Registro de Usuario</h4>
            <button type="button"
                class="text-gray-500 hover:text-red-500 text-2xl font-bold"
                onclick="cerrarUsuario()">&times;</button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <form id="form_usuario" class="grid grid-cols-1 gap-4">
                <input type="hidden" name="idusuario" id="idusuario">
                <input type="hidden" name="add_update" id="add_update" value="1">

                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Nombres <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        name="nombres"
                        id="nombres"
                        required
                        placeholder="Nombre..."
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white">
                </div>
                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Apellidos <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        name="apellidos"
                        id="apellidos"
                        required
                        placeholder="Apellidos..."
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email"
                        name="email"
                        id="email"
                        required
                        placeholder="Email..."
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Clave <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        name="clave"
                        id="clave"
                        required
                        placeholder="Clave..."
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Rol <span class="text-red-500">*</span>
                    </label>
                    <select name="idrol" id="idrol"
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white tipoRoles_list">
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Lugar de entrega <span class="text-red-500">*</span>
                    </label>
                    <textarea name="lugar_entraga" id="lugar_entraga" rows="3"
                        placeholder="Lugar de entrega..."
                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white"></textarea>
                </div>
            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center border-t px-6 py-3 bg-gray-50">
            <button type="button"
                onclick="cerrarUsuario()"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded">
                Cerrar
            </button>

            <button
                type="button"
                class="add-insert-upadate bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg
               text-sm flex items-center justify-center gap-2 transition shadow">
                <i class="fas fa-plus-circle"></i>
                Agregar
            </button>
        </div>

    </div>
</div>

<div id="usuarioCambioClaveModal"
    class="fixed inset-0 z-50 hidden bg-black bg-opacity-50 flex items-center justify-center">

    <!-- Caja modal -->
    <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">

        <!-- Header -->
        <div class="flex justify-between items-center border-b px-6 py-3">
            <h4 class="text-lg font-semibold text-gray-800">Cambiar clave</h4>
            <button type="button"
                class="text-gray-500 hover:text-red-500 text-2xl font-bold"
                onclick="cerrarCambiarClave()">&times;</button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <form id="form_newclave" class="grid grid-cols-1 gap-4">
                <input type="hidden" name="id" id="id">
                <input type="hidden" name="update_clave" id="update_clave" value="1">

                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Clave <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        name="newclave"
                        id="newclave"
                        required

                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white">
                </div>
                <!-- Categoría -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Confirmar clave <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                        name="confclave"
                        id="confclave"
                        required

                        class="mt-1 w-full border border-gray-300 rounded px-3 py-2 text-gray-900
                               focus:outline-none focus:ring focus:border-blue-400 bg-white">
                </div>



            </form>
        </div>

        <!-- Footer -->
        <div class="flex justify-between items-center border-t px-6 py-3 bg-gray-50">
            <button type="button"
                onclick="cerrarCambiarClave()"
                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded">
                Cerrar
            </button>

            <button
                type="button"
                class="upadate-clave bg-teal-500 hover:bg-teal-600 text-white px-5 py-2 rounded-lg
               text-sm flex items-center justify-center gap-2 transition shadow">
                <i class="fas fa-plus-circle"></i>
                Cambiar
            </button>
        </div>

    </div>
</div>


<!-- Modal backdrop -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <!-- Modal content -->
    <form name="delete_registro_form" id="delete_registro_form">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">¿Estás seguro que deseas eliminar este registro?</h2>
            <p class="text-sm text-gray-600 mb-6">Esta acción no se puede deshacer.</p>
            <input type="hidden" name="cid" id="cid">
            <input type="hidden" name="eliminar_registro" value="1">
            <div class="flex justify-end space-x-3">
                <button type="button" onclick="cancelarEliminar()" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded">Cancelar</button>
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded delete-registro-btn">Eliminar</button>
            </div>
        </div>
    </form>
</div>

<?php include_once("template/pie.php"); ?>
<script type="text/javascript" src="./js/usuario.js"></script>