
function load(page) {
	var query = $("#q").val();
	var per_page = 10;
	var parametros = { "action": "ajax", "page": page, 'query': query, 'per_page': per_page };
	$("#loader").fadeIn('slow');
	$.ajax({
		method: 'POST',
		url: '../pages/producto_paginar.php',
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
function abrirCambiarClave() {
	document.getElementById('usuarioCambioClaveModal').classList.remove('hidden');
}
function cerrarCambiarClave() {
	document.getElementById('usuarioCambioClaveModal').classList.add('hidden');
}

function abrirUsuario() {
	$('#form_producto').trigger("reset");
	$("input[name='id_producto']").val(0);
	document.getElementById('productoModal').classList.remove('hidden');
}
function cerrarUsuario() {
	document.getElementById('productoModal').classList.add('hidden');
}
function cancelarEliminar() {
	document.getElementById('deleteModal').classList.add('hidden');
}
$(document).ready(function () {
	// Renderiza la tabla


	load();


	function getUnidadMedida() {
		$.ajax({
			url: '../admin/classes/Commun.php',
			method: 'POST',
			data: { GET_UNIDADMEDIDA: 1 },
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {

					var catSelectHTML = '<option value="">Seleccione</option>';
					$.each(resp.message.enumerado, function (index, value) {
						catSelectHTML += '<option value="' + value.id_unidad + '">' + value.nombre + '</option>';
					});
					$(".tipoUnidadMedida_list").html(catSelectHTML);

				}
			}
		});
	}
	getUnidadMedida();
	function getTipoInpuesto() {
		$.ajax({
			url: '../admin/classes/Commun.php',
			method: 'POST',
			data: { GET_TIPOIMPUESTO: 1 },
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {

					var catSelectHTML = '<option value="">Seleccione</option>';
					$.each(resp.message.enumerado, function (index, value) {
						catSelectHTML += '<option value="' + value.id_impuesto + '">' + value.nombre + '</option>';
					});
					$(".tipoInpuesto_list").html(catSelectHTML);

				}
			}
		});
	}
	getTipoInpuesto();




	$(".add-insert-upadate").on("click", function () {
		if ($('#form_producto').valid() == false) {
			return;
		}
		$.ajax({
			url: '../admin/classes/Producto.php',
			method: 'POST',
			data: $("#form_producto").serialize(),
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {
					load();
					$('#form_producto').trigger("reset");
					toastr.success(resp.message);
				} else if (resp.status == 303) {
					toastr.error(resp.message);
				}
				cerrarUsuario();
			}
		})
	});
	$(document.body).on("click", ".edit-registro", function () {
		var producto = $.parseJSON($.trim($(this).children("span").html()));
		$("#id_producto").val(producto.id_producto);
		$("#descripcion").val(producto.descripcion);
		$("#id_unidad").val(producto.id_unidad);
		$("#precio_unitario").val(producto.precio_unitario);
		$("#id_impuesto").val(producto.id_impuesto);
		document.getElementById('productoModal').classList.remove('hidden');
	});

	$(document.body).on('click', '.delete-registro', function () {
		var cid = $(this).data('cid');
		$("input[name='cid']").val(cid);
		document.getElementById('deleteModal').classList.remove('hidden');
	});

	$(".delete-registro-btn").on('click', function (e) {
		e.preventDefault();
		$.ajax({
			url: '../admin/classes/Producto.php',
			method: 'POST',
			data: $("#delete_registro_form").serialize(),
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {
					toastr.success(resp.message);
					load(1);
				} else if (resp.status == 303) {
					toastr.error(resp.message);
				}
				cancelarEliminar();

			}
		});
	});
	$(document.body).on('click', '.change-pass', function () {
		var cid = $(this).data('cid');
		$("input[name='id']").val(cid);
		document.getElementById('usuarioCambioClaveModal').classList.remove('hidden');
	});


	$(".upadate-clave").on("click", function () {
		if ($('#form_newclave').valid() == false) {
			return;
		}
		$.ajax({
			url: '../admin/classes/Usuario.php',
			method: 'POST',
			data: $("#form_newclave").serialize(),
			success: function (response) {
				var resp = $.parseJSON(response);
				if (resp.status == 202) {
					load();
					$('#form_newclave').trigger("reset");
					toastr.success(resp.message);
				} else if (resp.status == 303) {
					toastr.error(resp.message);
				}
				cerrarCambiarClave();
			}
		})
	});
});
