
function load(page) {
	var query = $("#q").val();
	var per_page = 10;
	var parametros = { "action": "ajax", "page": page, 'query': query, 'per_page': per_page };
	$("#loader").fadeIn('slow');
	$.ajax({
		method: 'POST',
		url: '../pages/ventas_paginar.php',
		data: parametros,
		beforeSend: function (objeto) {
			$("#loader").html("Cargando...");
		},
		success: function (data) {
			$(".outer_div").html(data).fadeIn('slow');
			$("#loader").html("");
		}
	});
}
function abrirView(id) {
	$.ajax({
		url: '../admin/classes/Factura.php',
		method: 'POST',
		data: { get_factura_by_id: 1, id_factura: id },
		success: function (response) {
			var resp = $.parseJSON(response);
			if (resp) {
				var html = '<div class="border-b pb-4 mb-4">';
				html += '<div class="grid grid-cols-2 md:grid-cols-3 gap-4">';
				html += '<div><span class="text-sm font-medium text-gray-500">Serie:</span><p class="text-gray-800">' + resp.serie + '-' + resp.numero_factura + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Fecha:</span><p class="text-gray-800">' + resp.fecha_emision + ' ' + resp.hora_emision + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Tipo:</span><p class="text-gray-800">' + (resp.tipo_comprobante_nombre || '') + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Cliente:</span><p class="text-gray-800">' + (resp.cliente_nombre || '') + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Doc. Cliente:</span><p class="text-gray-800">' + (resp.cliente_doc || '') + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Moneda:</span><p class="text-gray-800">' + (resp.moneda_nombre || '') + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Método Pago:</span><p class="text-gray-800">' + (resp.metodo_pago_nombre || '') + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Estado:</span><p class="text-gray-800">' + (resp.estado_nombre || '') + '</p></div>';
				html += '<div><span class="text-sm font-medium text-gray-500">Empresa:</span><p class="text-gray-800">' + (resp.empresa_nombre || '') + '</p></div>';
				html += '</div>';
				if (resp.observacion) html += '<div class="mt-2"><span class="text-sm font-medium text-gray-500">Observación:</span><p class="text-gray-800">' + resp.observacion + '</p></div>';
				html += '</div>';

				html += '<h5 class="font-semibold text-gray-700 mb-2">Detalle de Productos</h5>';
				html += '<table class="min-w-full divide-y divide-gray-200 text-sm">';
				html += '<thead class="bg-gray-50"><tr>';
				html += '<th class="px-4 py-2 text-left">Producto</th>';
				html += '<th class="px-4 py-2 text-right">Cantidad</th>';
				html += '<th class="px-4 py-2 text-right">P. Unitario</th>';
				html += '<th class="px-4 py-2 text-right">V. Venta</th>';
				html += '<th class="px-4 py-2 text-right">Impuesto</th>';
				html += '<th class="px-4 py-2 text-right">Total</th>';
				html += '</tr></thead><tbody>';
				if (resp.detalle) {
					$.each(resp.detalle, function (i, d) {
						html += '<tr class="border-t">';
						html += '<td class="px-4 py-2">' + (d.producto_nombre || '') + '</td>';
						html += '<td class="px-4 py-2 text-right">' + parseFloat(d.cantidad).toFixed(3) + '</td>';
						html += '<td class="px-4 py-2 text-right">' + parseFloat(d.precio_unitario).toFixed(2) + '</td>';
						html += '<td class="px-4 py-2 text-right">' + parseFloat(d.valor_venta).toFixed(2) + '</td>';
						html += '<td class="px-4 py-2 text-right">' + parseFloat(d.valor_impuesto).toFixed(2) + '</td>';
						html += '<td class="px-4 py-2 text-right font-semibold">' + parseFloat(d.total_linea).toFixed(2) + '</td>';
						html += '</tr>';
					});
				}
				html += '</tbody></table>';

				html += '<div class="flex justify-end mt-4 space-x-6 text-sm">';
				html += '<div><span class="text-gray-500">Subtotal:</span> <span class="font-semibold">' + parseFloat(resp.subtotal).toFixed(2) + '</span></div>';
				html += '<div><span class="text-gray-500">Impuestos:</span> <span class="font-semibold">' + parseFloat(resp.total_impuestos).toFixed(2) + '</span></div>';
				html += '<div><span class="text-gray-500">Total:</span> <span class="font-bold text-teal-600">' + parseFloat(resp.total).toFixed(2) + '</span></div>';
				html += '</div>';

				$("#viewContent").html(html);
				document.getElementById('viewModal').classList.remove('hidden');
			}
		}
	});
}
function cerrarView() {
	document.getElementById('viewModal').classList.add('hidden');
}
function cerrarDelete() {
	document.getElementById('deleteModal').classList.add('hidden');
}
$(document).ready(function () {
	load();

	$(document.body).on('click', '.delete-registro', function () {
		var cid = $(this).data('cid');
		$("input[name='cid']").val(cid);
		document.getElementById('deleteModal').classList.remove('hidden');
	});

	$("#delete_form").on('submit', function (e) {
		e.preventDefault();
		$.ajax({
			url: '../admin/classes/Factura.php',
			method: 'POST',
			data: $("#delete_form").serialize(),
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {
					toastr.success(resp.message);
					load(1);
				} else if (resp.status == 303) {
					toastr.error(resp.message);
				}
				cerrarDelete();
			}
		});
	});

	$(document.body).on('click', '.view-registro', function () {
		var id = $(this).data('id');
		abrirView(id);
	});
});
