function formatMoney(n) {
    return 'S/ ' + parseFloat(n).toLocaleString('es-PE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function cargarDashboard() {
    $('#dashboardContent').html(`
        <div class="text-center py-20 text-gray-400">
            <i class="fas fa-spinner fa-spin text-4xl mb-4"></i>
            <p>Cargando dashboard...</p>
        </div>
    `);

    $.ajax({
        type: 'POST',
        url: '../admin/classes/Dashboard.php',
        data: { get_dashboard_data: 1 },
        dataType: 'json',
        success: function (r) {
            if (r.status !== 202) {
                $('#dashboardContent').html('<div class="bg-red-50 text-red-600 p-4 rounded-xl">Error al cargar datos</div>');
                return;
            }
            renderDashboard(r);
        },
        error: function () {
            $('#dashboardContent').html('<div class="bg-red-50 text-red-600 p-4 rounded-xl">Error de conexión</div>');
        }
    });
}

function renderDashboard(d) {
    var html = '';

    // CARDS
    html += '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">';
    html += card('Productos Activos', d.total_productos, 'fa-box', 'bg-blue-500');
    html += card('Clientes Activos', d.total_clientes, 'fa-users', 'bg-green-500');
    html += card('Ventas Totales', d.total_ventas, 'fa-receipt', 'bg-purple-500');
    html += card('Ingresos Totales', formatMoney(d.total_ingresos), 'fa-dollar-sign', 'bg-amber-500');
    html += '</div>';

    // HOY
    html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">';
    html += '<div class="bg-white p-5 rounded-xl shadow">';
    html += '<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3"><i class="fas fa-calendar-day mr-2"></i>Ventas de Hoy</h3>';
    html += '<div class="grid grid-cols-2 gap-4">';
    html += '<div><p class="text-2xl font-bold text-gray-800">' + (d.ventas_hoy.total || 0) + '</p><p class="text-xs text-gray-400">Comprobantes</p></div>';
    html += '<div><p class="text-2xl font-bold text-emerald-600">' + formatMoney(d.ventas_hoy.monto || 0) + '</p><p class="text-xs text-gray-400">Monto</p></div>';
    html += '</div></div>';

    // VENTAS POR MES (mini tabla resumen)
    html += '<div class="bg-white p-5 rounded-xl shadow">';
    html += '<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3"><i class="fas fa-chart-bar mr-2"></i>Ventas por Mes</h3>';
    html += '<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b text-left text-gray-400"><th class="pb-2">Mes</th><th class="pb-2 text-right">Ventas</th><th class="pb-2 text-right">Ingresos</th></tr></thead><tbody>';
    if (d.ventas_por_mes && d.ventas_por_mes.length) {
        d.ventas_por_mes.forEach(function (m) {
            html += '<tr class="border-b border-gray-100"><td class="py-2">' + m.mes + '</td><td class="py-2 text-right">' + m.total_ventas + '</td><td class="py-2 text-right font-medium">' + formatMoney(m.total_ingresos) + '</td></tr>';
        });
    } else {
        html += '<tr><td colspan="3" class="py-4 text-center text-gray-400">Sin datos</td></tr>';
    }
    html += '</tbody></table></div></div></div>';

    // TABLAS
    html += '<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">';

    // VENTAS RECIENTES
    html += '<div class="bg-white p-5 rounded-xl shadow">';
    html += '<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3"><i class="fas fa-clock mr-2"></i>Últimas Ventas</h3>';
    html += '<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b text-gray-400"><th class="pb-2 text-left">#</th><th class="pb-2 text-left">Cliente</th><th class="pb-2 text-left">Total</th><th class="pb-2 text-right">Estado</th></tr></thead><tbody>';
    if (d.ventas_recientes && d.ventas_recientes.length) {
        d.ventas_recientes.forEach(function (v) {
            var estadoColor = v.estado === 'COMPLETADO' ? 'text-green-600 bg-green-50' : v.estado === 'ANULADO' ? 'text-red-600 bg-red-50' : 'text-yellow-600 bg-yellow-50';
            html += '<tr class="border-b border-gray-100">';
            html += '<td class="py-2 text-left">' + v.serie + '-' + v.correlativo + '</td>';
            html += '<td class="py-2 text-left">' + (v.cliente_nombre || '-') + '</td>';
            html += '<td class="py-2 text-left font-medium">' + formatMoney(v.total) + '</td>';
            html += '<td class="py-2 text-right"><span class="text-xs px-2 py-1 rounded-full ' + estadoColor + '">' + v.estado + '</span></td>';
            html += '</tr>';
        });
    } else {
        html += '<tr><td colspan="4" class="py-4 text-center text-gray-400">Sin ventas recientes</td></tr>';
    }
    html += '</tbody></table></div></div>';

    // STOCK BAJO
    html += '<div class="bg-white p-5 rounded-xl shadow">';
    html += '<h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3"><i class="fas fa-exclamation-triangle mr-2 text-amber-500"></i>Productos con Stock Bajo</h3>';
    html += '<div class="overflow-x-auto"><table class="w-full text-sm"><thead><tr class="border-b text-left text-gray-400"><th class="pb-2">Producto</th><th class="pb-2">Categoría</th><th class="pb-2 text-right">Stock</th><th class="pb-2 text-right">Mínimo</th></tr></thead><tbody>';
    if (d.stock_bajo && d.stock_bajo.length) {
        d.stock_bajo.forEach(function (p) {
            var peligro = p.stock_actual === 0 ? 'text-red-600 font-bold' : 'text-amber-600';
            html += '<tr class="border-b border-gray-100">';
            html += '<td class="py-2">' + p.nombre + '</td>';
            html += '<td class="py-2 text-gray-500">' + (p.categoria_nombre || '-') + '</td>';
            html += '<td class="py-2 text-right ' + peligro + '">' + p.stock_actual + '</td>';
            html += '<td class="py-2 text-right text-gray-500">' + p.stock_minimo + '</td>';
            html += '</tr>';
        });
    } else {
        html += '<tr><td colspan="4" class="py-4 text-center text-gray-400">Todo en stock óptimo</td></tr>';
    }
    html += '</tbody></table></div></div>';

    html += '</div>';

    $('#dashboardContent').html(html);
}

function card(label, value, icon, bgColor) {
    return '<div class="bg-white p-5 rounded-xl shadow flex items-center gap-4">\
        <div class="w-12 h-12 rounded-xl ' + bgColor + ' text-white flex items-center justify-center text-xl">\
            <i class="fas ' + icon + '"></i>\
        </div>\
        <div>\
            <p class="text-xs text-gray-400 uppercase tracking-wide">' + label + '</p>\
            <p class="text-xl font-bold text-gray-800">' + value + '</p>\
        </div>\
    </div>';
}

$(document).ready(function () {
    cargarDashboard();
});
