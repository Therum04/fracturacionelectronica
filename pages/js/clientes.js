
function load(page) {
	var query = $("#q").val();
	var per_page = 10;
	var parametros = { "action": "ajax", "page": page, 'query': query, 'per_page': per_page };
	$("#loader").fadeIn('slow');
	$.ajax({
		method: 'POST',
		url: '../pages/clientes_paginar.php',
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
function abrirModal() {
	$('#form_cliente').trigger("reset");
	$("input[name='id_cliente']").val(0);
	document.getElementById('clienteModal').classList.remove('hidden');
}
function cerrarModal() {
	document.getElementById('clienteModal').classList.add('hidden');
}
function cerrarDelete() {
	document.getElementById('deleteModal').classList.add('hidden');
}
$(document).ready(function () {
	load();

	function getTipoDocumento() {
		$.ajax({
			url: '../admin/classes/Commun.php',
			method: 'POST',
			data: { GET_TIPODOCUMENTOIDENTIDAD: 1 },
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {
					var catSelectHTML = '<option value="">Seleccione</option>';
					$.each(resp.message.enumerado, function (index, value) {
						catSelectHTML += '<option value="' + value.id_tipo_doc + '">' + value.nombre + '</option>';
					});
					$(".tipoDocumento_list").html(catSelectHTML);
				}
			}
		});
	}
	getTipoDocumento();

	$(".btn-guardar").on("click", function () {
		if ($('#form_cliente').valid() == false) {
			return;
		}
		$.ajax({
			url: '../admin/classes/Cliente.php',
			method: 'POST',
			data: $("#form_cliente").serialize(),
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {
					load();
					$('#form_cliente').trigger("reset");
					toastr.success(resp.message);
				} else if (resp.status == 303) {
					toastr.error(resp.message);
				}
				cerrarModal();
			}
		})
	});
	$(document.body).on("click", ".edit-registro", function () {
		var cliente = $.parseJSON($.trim($(this).children("span").html()));
		$("#id_cliente").val(cliente.id_cliente);
		$("#id_tipo_doc").val(cliente.id_tipo_doc);
		$("#numero_doc").val(cliente.numero_doc);
		$("#nombre_razon_social").val(cliente.nombre_razon_social);
		$("#direccion").val(cliente.direccion);
		$("#correo").val(cliente.correo);
		$("#telefono").val(cliente.telefono);
		document.getElementById('clienteModal').classList.remove('hidden');
	});

	$(document.body).on('click', '.delete-registro', function () {
		var cid = $(this).data('cid');
		$("input[name='cid']").val(cid);
		document.getElementById('deleteModal').classList.remove('hidden');
	});

	$("#delete_form").on('submit', function (e) {
		e.preventDefault();
		$.ajax({
			url: '../admin/classes/Cliente.php',
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
});
