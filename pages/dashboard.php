<?php include_once("template/cabecera.php"); ?>
<main class="flex-1 p-5 w-full">
    <div class="bg-white p-6 rounded-xl shadow mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
                <p class="text-sm text-gray-500">Resumen general del sistema</p>
            </div>
            <button onclick="cargarDashboard()" class="bg-teal-500 hover:bg-teal-600 text-white px-4 py-2 rounded-lg text-sm flex items-center gap-2 transition">
                <i class="fas fa-sync-alt"></i> Actualizar
            </button>
        </div>
    </div>

    <div id="dashboardContent">
        <div class="text-center py-20 text-gray-400">
            <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
            <p>Cargando dashboard...</p>
        </div>
    </div>
</main>

<?php include_once("template/pie.php"); ?>
<script src="./js/dashboard.js"></script>
