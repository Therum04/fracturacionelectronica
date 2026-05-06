<!DOCTYPE html>
<html lang="es">
<?php
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

session_start();

$logueado = false;
$nombre = null;
$rol = null;
$idusuario = null;

if (isset($_SESSION['idusuarios'])) {
    $logueado = true;
    $nombre = $_SESSION['nombres'];
    $rol = $_SESSION['enum_rol'];
    $idusuario = $_SESSION['idusuarios'];
}
$cartCount = 0;

if (!empty($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cartCount += $item['cantidad']; // suma cantidades
    }
}
?>

<head>
    <meta charset="UTF-8">
    <title>Productos | Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="./css/tailwind.min.css"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./js/jquery.validate.min.js"></script>
    <script src="./js/toastr.min.js"></script>

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
                <nav class="space-y-3 mt-6">
                    <a href="presentacion.php"
                        class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'presentacion.php' ? $active : $normal ?>">
                        🏠 Inicio
                    </a>
                    <?php if ($logueado && $rol == 1): ?>
                        <a href="producto.php"
                            class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'producto.php' ? $active : $normal ?>">
                            <i class="fa fa-tags"></i> Productos
                        </a>
                        <a href="categoria.php"
                            class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'categoria.php' ? $active : $normal ?>">
                            📂 Categorías
                        </a>
                        <a href="baner.php"
                            class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'baner.php' ? $active : $normal ?>">
                            🖼 Banner
                        </a>
                        <a href="perfil.php"
                            class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'perfil.php' ? $active : $normal ?>">
                            👤 Perfil
                        </a>
                        <a href="usuario.php"
                            class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'usuario.php' ? $active : $normal ?>">
                            👤 Usuarios
                        </a>
                    <?php endif; ?>
                    <?php if ($logueado && ($rol == 1 || $rol == 2 || $rol == 3)): ?>
                        <a href="pedidos.php"
                            class="flex items-center gap-3 p-3 rounded-lg
                    <?= $pagina == 'pedidos.php' ? $active : $normal ?>">
                            <i class="fas fa-clipboard-list"></i> </i> Pedidos
                        </a>



                    <?php endif; ?>
                    <a href="carrito.php"
                        class="flex items-center gap-2 p-3 rounded-lg
                   <?= $pagina == 'carrito.php' ? $active : $normal ?>">
                        🛒 Carrito
                        <span id="cartBadge"
                            class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full
                   <?= $cartCount > 0 ? '' : 'hidden' ?>">
                            <?= $cartCount ?>
                        </span>

                    </a>

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