<?php
date_default_timezone_set('America/Lima');
require_once __DIR__ . '/../admin/classes/Database.php';

$db = new Database();
$con = $db->connect();

$id = (int)($_GET['id'] ?? 0);
$formato = $_GET['formato'] ?? 'ticket';

$venta = null;
$emisor = null;
$detalle = [];

if ($id) {
    $stmt = $con->prepare("SELECT v.*,
        COALESCE(c.razon_social, CONCAT(c.nombres, ' ', c.apellido_paterno, ' ', c.apellido_materno)) AS cliente_nombre,
        c.numero_documento AS cliente_doc,
        c.tipo_documento AS cliente_tipo_doc,
        c.direccion AS cliente_direccion,
        c.email AS cliente_email,
        c.telefono AS cliente_telefono
        FROM ventas v LEFT JOIN clientes c ON v.id_cliente = c.id WHERE v.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $venta = $stmt->get_result()->fetch_assoc();

    $stmt_d = $con->prepare("SELECT vd.*, p.nombre AS producto_nombre, p.codigo AS producto_codigo
        FROM venta_detalle vd LEFT JOIN productos p ON vd.id_producto = p.id WHERE vd.id_venta = ?");
    $stmt_d->bind_param("i", $id);
    $stmt_d->execute();
    $detalle = $stmt_d->get_result()->fetch_all(MYSQLI_ASSOC);

    $r = $con->query("SELECT * FROM emisor WHERE id = 1 LIMIT 1");
    $emisor = $r->fetch_assoc();
}

if (!$venta) {
    die('Venta no encontrada');
}

$esTicket = $formato === 'ticket';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir - <?= $venta['serie'] ?>-<?= str_pad($venta['correlativo'], 6, '0', STR_PAD_LEFT) ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Courier New', monospace;
            font-size: <?= $esTicket ? '12px' : '14px' ?>;
            color: #000;
            background: #fff;
        }
        .ticket {
            width: <?= $esTicket ? '80mm' : '210mm' ?>;
            margin: 0 auto;
            padding: <?= $esTicket ? '5px 10px' : '20px' ?>;
        }
        .header { text-align: center; margin-bottom: 10px; }
        .header h2 { font-size: <?= $esTicket ? '14px' : '18px' ?>; font-weight: bold; text-transform: uppercase; }
        .header p { font-size: <?= $esTicket ? '10px' : '13px' ?>; line-height: 1.4; }
        .line { border-top: 1px dashed #000; margin: 6px 0; }
        .line-solid { border-top: 1px solid #000; margin: 6px 0; }
        .info-table { width: 100%; font-size: <?= $esTicket ? '10px' : '13px' ?>; }
        .info-table td { padding: 1px 0; vertical-align: top; }
        .info-table td:first-child { width: <?= $esTicket ? '90px' : '130px' ?>; }
        .items { width: 100%; border-collapse: collapse; font-size: <?= $esTicket ? '10px' : '13px' ?>; margin: 6px 0; }
        .items th {
            border-bottom: 1px solid #000;
            border-top: 1px solid #000;
            padding: 4px 2px;
            text-align: left;
            font-size: <?= $esTicket ? '9px' : '12px' ?>;
        }
        .items th:nth-child(2),
        .items th:nth-child(3),
        .items th:nth-child(4) { text-align: right; }
        .items td { padding: 3px 2px; vertical-align: top; }
        .items td:nth-child(2),
        .items td:nth-child(3),
        .items td:nth-child(4) { text-align: right; white-space: nowrap; }
        .totals { width: 100%; font-size: <?= $esTicket ? '10px' : '13px' ?>; margin-top: 4px; }
        .totals td { padding: 2px 0; }
        .totals td:first-child { text-align: right; width: <?= $esTicket ? '60%' : '80%' ?>; font-weight: bold; }
        .totals td:last-child { text-align: right; width: <?= $esTicket ? '40%' : '20%' ?>; font-weight: bold; }
        .totals .grand-total { font-size: <?= $esTicket ? '14px' : '18px' ?>; }
        .footer { text-align: center; margin-top: 10px; font-size: <?= $esTicket ? '9px' : '12px' ?>; }
        .sunat-info { font-size: <?= $esTicket ? '8px' : '11px' ?>; text-align: center; margin-top: 8px; }
        .sunat-info p { margin: 1px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .mono { font-family: 'Courier New', monospace; }

        @media print {
            body { background: #fff; }
            .no-print { display: none; }
            @page { margin: 0; }
        }

        .no-print {
            text-align: center;
            padding: 20px;
            background: #f0f0f0;
            margin-bottom: 10px;
        }
        .no-print button {
            padding: 10px 30px;
            font-size: 16px;
            background: #2563eb;
            color: #fff;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            margin: 0 5px;
        }
        .no-print button:hover { background: #1d4ed8; }
        .no-print .btn-ticket { background: #059669; }
        .no-print .btn-ticket:hover { background: #047857; }
    </style>
</head>
<body>

<div class="no-print">
    <button class="btn-ticket" onclick="window.print()">🖨 Imprimir Ticket</button>
    <button onclick="location.href='print_venta.php?id=<?= $id ?>&formato=a4'">📄 Formato A4</button>
    <button onclick="location.href='print_venta.php?id=<?= $id ?>&formato=ticket'">🧾 Formato Ticket</button>
    <button onclick="window.close()">✕ Cerrar</button>
</div>

<div class="ticket">

    <!-- HEADER -->
    <div class="header">
        <h2><?= htmlspecialchars($emisor['nom_comercial'] ?? $emisor['razon_social'] ?? '') ?></h2>
        <p><?= htmlspecialchars($emisor['razon_social'] ?? '') ?></p>
        <p><strong>RUC: <?= htmlspecialchars($emisor['ruc'] ?? '') ?></strong></p>
        <p><?= htmlspecialchars($emisor['direccion'] ?? '') ?></p>
        <?php if ($emisor['direccion_distrito'] ?? false): ?>
            <p><?= htmlspecialchars($emisor['direccion_departamento'] ?? '') ?> - <?= htmlspecialchars($emisor['direccion_provincia'] ?? '') ?> - <?= htmlspecialchars($emisor['direccion_distrito'] ?? '') ?></p>
        <?php endif; ?>
        <p><?= htmlspecialchars($emisor['email'] ?? '') ?></p>
    </div>

    <div class="line"></div>

    <!-- TIPO COMPROBANTE -->
    <div class="header">
        <h2><?= $venta['tipo_comprobante'] === 'FACTURA' ? 'FACTURA ELECTRÓNICA' : 'BOLETA ELECTRÓNICA' ?></h2>
        <h2><?= $venta['serie'] ?> - <?= str_pad($venta['correlativo'], 6, '0', STR_PAD_LEFT) ?></h2>
    </div>

    <div class="line"></div>

    <!-- INFO -->
    <table class="info-table">
        <tr>
            <td><strong>Fecha:</strong></td>
            <td><?= date('d/m/Y H:i', strtotime($venta['fecha_emision'])) ?></td>
        </tr>
        <tr>
            <td><strong>Cliente:</strong></td>
            <td><?= htmlspecialchars($venta['cliente_nombre'] ?? 'CLIENTE GENERAL') ?></td>
        </tr>
        <tr>
            <td><strong>RUC/DNI:</strong></td>
            <td><?= htmlspecialchars($venta['cliente_doc'] ?? '-') ?></td>
        </tr>
        <?php if (!empty($venta['cliente_direccion'])): ?>
        <tr>
            <td><strong>Dirección:</strong></td>
            <td><?= htmlspecialchars($venta['cliente_direccion']) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td><strong>Método Pago:</strong></td>
            <td><?= htmlspecialchars($venta['metodo_pago'] ?? '-') ?></td>
        </tr>
    </table>

    <div class="line"></div>

    <!-- ITEMS -->
    <table class="items">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Cant</th>
                <th>P.Unit</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $item): ?>
            <tr>
                <td><?= htmlspecialchars($item['producto_nombre'] ?? 'Producto') ?></td>
                <td><?= (int)$item['cantidad'] ?></td>
                <td><?= number_format($item['precio_unitario'], 2) ?></td>
                <td><?= number_format($item['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="line"></div>

    <!-- TOTALS -->
    <table class="totals">
        <tr>
            <td>Subtotal:</td>
            <td>S/ <?= number_format($venta['subtotal'], 2) ?></td>
        </tr>
        <tr>
            <td>IGV (18%):</td>
            <td>S/ <?= number_format($venta['igv'], 2) ?></td>
        </tr>
        <tr class="grand-total">
            <td><strong>TOTAL:</strong></td>
            <td><strong>S/ <?= number_format($venta['total'], 2) ?></strong></td>
        </tr>
    </table>

    <?php if ($venta['monto_recibido'] > 0): ?>
    <table class="totals">
        <tr>
            <td>Monto Recibido:</td>
            <td>S/ <?= number_format($venta['monto_recibido'], 2) ?></td>
        </tr>
        <tr>
            <td>Cambio:</td>
            <td>S/ <?= number_format($venta['cambio'], 2) ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <div class="line-solid"></div>

    <!-- SUNAT INFO -->
    <div class="sunat-info">
        <p><strong>Autorizado mediante:</strong> Resolución de SUNAT</p>
        <p><strong>Estado SUNAT:</strong> <?= strtoupper($venta['sunat_estado'] ?? 'PENDIENTE') ?></p>
        <?php if ($venta['sunat_ticket']): ?>
            <p><strong>Ticket:</strong> <?= htmlspecialchars($venta['sunat_ticket']) ?></p>
        <?php endif; ?>
        <p>Representación impresa del comprobante electrónico</p>
        <p>Consulte en www.sunat.gob.pe</p>
    </div>

</div>

<script>
    window.onload = function() {
        <?php if ($esTicket): ?>
        setTimeout(function() { window.print(); }, 500);
        <?php endif; ?>
    };
</script>

</body>
</html>
