<?php
session_start();
$pagina = basename($_SERVER['PHP_SELF']);
$active = "bg-teal-50 border border-teal-200 text-teal-600 font-medium";
$normal = "hover:bg-gray-100";
/* session_start();
if (!isset($_SESSION['idusuario'])) {
    header("Location: ../index.php");
}
$nombre = $_SESSION['nombres'];
$rol = $_SESSION['idrol'];
$idusuario = $_SESSION['idusuario']; */

$logueado = false;
$nombre = null;
$rol = null;
$idusuario = null;

if (isset($_SESSION['idusuarios'])) {
    $logueado = true;
    $nombre = $_SESSION['nombres'];
    $rol = $_SESSION['enum_rol'];
    $desRol = $_SESSION['rol'];
    $idusuario = $_SESSION['idusuarios'];
}
$cartCount = 0;

if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cartCount += $item['cantidad']; // suma cantidades
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Productos | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./css/tailwind.min.css"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/jquery.validate.min.js"></script>
    <script src="./js/toastr.min.js"></script>
    <script>
        function toggleSubmenu(btn) {
            var submenu = btn.nextElementSibling;
            var chevron = btn.querySelector('.fa-chevron-down');
            while (submenu && !submenu.classList.contains('submenu')) {
                submenu = submenu.nextElementSibling;
            }
            if (submenu) {
                submenu.classList.toggle('hidden');
                if (chevron) chevron.classList.toggle('rotated');
            }
        }
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.submenu').forEach(function (el) {
                var links = el.querySelectorAll('a');
                var shouldOpen = false;
                links.forEach(function (link) {
                    var page = window.location.pathname.split('/').pop();
                    if (link.getAttribute('href') === page) {
                        shouldOpen = true;
                    }
                });
                if (shouldOpen) {
                    el.classList.remove('hidden');
                    var btn = el.previousElementSibling;
                    while (btn && btn.tagName !== 'BUTTON') {
                        btn = btn.previousElementSibling;
                    }
                    if (btn) {
                        var chevron = btn.querySelector('.fa-chevron-down');
                        if (chevron) chevron.classList.add('rotated');
                    }
                }
            });
        });
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script type="text/javascript" src="./js/validation.js"></script>
    <style>
        .error {
            border-color: #dc3545;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 80%;
            color: #dc3545;
        }
        .submenu {
            overflow: hidden;
            transition: max-height 0.25s ease;
        }
        .submenu.open {
            max-height: 300px;
        }
        .chevron.rotated {
            transform: rotate(180deg);
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="flex min-h-screen">

        <!-- SIDEBAR -->
        <aside id="sidebar"
            class="fixed md:static z-40 w-80 h-screen bg-white shadow-lg
            flex flex-col justify-between
            p-6 transform -translate-x-full md:translate-x-0 transition-transform duration-300">

            <!-- TOP -->
            <div>
                <!-- LOGO -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 bg-teal-500 text-white flex items-center justify-center rounded-full font-bold">
                        ES
                    </div>
                    <div>
                        <p class="font-semibold">FACTURACION ELECTRONICA</p>
                        <span class="text-sm text-gray-500">Dashboard</span><br>
                        <?php if ($logueado): ?>
                            <p><strong>Bienvenido: <?= htmlspecialchars($nombre) ?></strong></p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- MENU -->
                <nav class="space-y-1 mt-6">

                    <a href="presentacion.php"
                        class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'presentacion.php' ? $active : $normal ?>">
                        <i class="fa fa-home"></i> Inicio
                    </a>

                    <?php if ($logueado && $rol == 1): ?>

                        <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between gap-3 p-3 rounded-lg text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 hover:bg-gray-50">
                            <span><i class="fa fa-cogs mr-2"></i> Configuración</span>
                            <i class="fa fa-chevron-down text-[10px] transition-transform duration-200"></i>
                        </button>
                        <div class="submenu hidden space-y-1">
                           <!--  <a href="empresa.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'empresa.php' ? $active : $normal ?>">
                                <i class="fa fa-building"></i> Empresa
                            </a> -->
                            <a href="emisor.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'emisor.php' ? $active : $normal ?>">
                                <i class="fa fa-file-invoice"></i> Emisor
                            </a>
                        </div>

                        <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between gap-3 p-3 rounded-lg text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 hover:bg-gray-50">
                            <span><i class="fa fa-boxes mr-2"></i> Inventario</span>
                            <i class="fa fa-chevron-down text-[10px] transition-transform duration-200"></i>
                        </button>
                        <div class="submenu hidden space-y-1">
                            <a href="categoria.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'categoria.php' ? $active : $normal ?>">
                                <i class="fa fa-list"></i> Categorías
                            </a>
                            <a href="producto.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'producto.php' ? $active : $normal ?>">
                                <i class="fa fa-box"></i> Productos
                            </a>
                            <a href="proveedor.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'proveedor.php' ? $active : $normal ?>">
                                <i class="fa fa-truck"></i> Proveedores
                            </a>
                        </div>

                        <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between gap-3 p-3 rounded-lg text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 hover:bg-gray-50">
                            <span><i class="fa fa-users mr-2"></i> Personas</span>
                            <i class="fa fa-chevron-down text-[10px] transition-transform duration-200"></i>
                        </button>
                        <div class="submenu hidden space-y-1">
                            <a href="usuario.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'usuario.php' ? $active : $normal ?>">
                                <i class="fa fa-user-gear"></i> Usuarios
                            </a>
                            <a href="clientes.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'clientes.php' ? $active : $normal ?>">
                                <i class="fa fa-user"></i> Clientes
                            </a>
                        </div>

                    <?php endif; ?>

                    <?php if ($logueado): ?>

                        <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between gap-3 p-3 rounded-lg text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 hover:bg-gray-50">
                            <span><i class="fa fa-file-invoice-dollar mr-2"></i> Facturación</span>
                            <i class="fa fa-chevron-down text-[10px] transition-transform duration-200"></i>
                        </button>
                        <div class="submenu hidden space-y-1">
                            <a href="ventas.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'ventas.php' ? $active : $normal ?>">
                                <i class="fa fa-receipt"></i> Ventas
                            </a>
                            <a href="factura.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'factura.php' ? $active : $normal ?>">
                                <i class="fa fa-plus-circle"></i> Nueva Factura
                            </a>
                        </div>

                        <button onclick="toggleSubmenu(this)" class="w-full flex items-center justify-between gap-3 p-3 rounded-lg text-xs font-semibold uppercase tracking-wider text-gray-400 hover:text-gray-600 hover:bg-gray-50">
                            <span><i class="fa fa-id-card mr-2"></i> Cuenta</span>
                            <i class="fa fa-chevron-down text-[10px] transition-transform duration-200"></i>
                        </button>
                        <div class="submenu hidden space-y-1">
                            <a href="perfil.php"
                                class="flex items-center gap-3 p-3 ml-4 rounded-lg
                    <?= $pagina == 'perfil.php' ? $active : $normal ?>">
                                <i class="fa fa-user-gear"></i> Perfil
                            </a>
                        </div>

                    <?php endif; ?>

                </nav>
            </div>
            <?php if ($logueado): ?>
                <!-- BOTTOM -->
                <a href="cerrarsesion.php"
                    class="w-full block text-center border border-red-300 text-red-500 p-3 rounded-lg hover:bg-red-50">
                    🚪 Cerrar sesión
                </a>
            <?php else: ?>
                <!-- REGISTRAR CUENTA -->
                <a href="../index.php"
                    class="w-full block text-center border border-emerald-300 text-emerald-600 p-3 rounded-lg hover:bg-emerald-50">
                    📝 Registrar cuenta
                </a>
            <?php endif; ?>
        </aside>


        <!-- OVERLAY MOBILE -->
        <div id="overlay" class="fixed inset-0 bg-black/40 hidden md:hidden" onclick="toggleMenu()"></div>