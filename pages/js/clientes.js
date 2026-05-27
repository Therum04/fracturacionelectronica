
let clienteDocumentoTimer = null;
let clienteDocumentoCargando = false;
let clienteDocumentoUltimoResuelto = '';
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

function limpiarCampos() {
	$("#nombres").val('');
	$("#nombresHidden").val('');
	$("#apellidoPaterno").val('');
	$("#apellidoPaternoHidden").val('');
	$("#apellidoMaterno").val('');
	$("#apellidoMaternoHidden").val('');
	$("#razon_social").val('');
	$("#razonSocialHidden").val('');
	$("#nombre_comercial").val('');
	$("#nombreComercialHidden").val('');
	$("#condicion").val('');
	$("#condicionHidden").val('');
	$("#estado_ruc").val('');
	$("#estadoRucHidden").val('');
	$("#direccion").val('');
	$("#codigo_ubigeo").val('');
	$("#codigoUbigeoHidden").val('');
	$("#pais").val('PE');
	$("#paisHidden").val('PE');
	$("#departamento").val('');
	$("#departamentoHidden").val('');
	$("#provincia").val('');
	$("#provinciaHidden").val('');
	$("#distrito").val('');
	$("#distritoHidden").val('');
	$("#ciudad").val('');
	$("#telefono").val('');
	$("#sexo").val('');
	$("#fechaNacimiento").val('');
}

function normalizarTexto(valor) {
	return (valor || '').toString().trim();
}
function buscarDatosDocumento() {
	const tipo = ($("#tipoDocumento").val() || 'DNI').toUpperCase();
	const numero = normalizarTexto($("#numeroDocumento").val()).replace(/\s+/g, '');

	if (clienteDocumentoCargando) return;
	if (numero && numero === clienteDocumentoUltimoResuelto) return;

	if (tipo === 'DNI' && numero.length === 8) {
		clienteDocumentoCargando = true;
		toastr.info('Consultando DNI...');

		$.ajax({
			url: '../admin/classes/ConsultaApi.php',
			method: 'POST',
			data: { consultar_dni: numero },
			dataType: 'json',
			timeout: 15000,
			success: function (data) {
				if (data && (data.respuesta === 'ok' || data.api?.existe === 'S')) {
					aplicarRespuestaDni(data);
					clienteDocumentoUltimoResuelto = numero;
					toastr.success('Datos de DNI cargados');
				} else {
					toastr.warning('No se encontraron datos para este DNI');
				}
			},
			error: function () {
				toastr.error('No se pudo consultar el DNI');
			},
			complete: function () {
				clienteDocumentoCargando = false;
			}
		});
		return;
	}

	if (tipo === 'RUC' && numero.length === 11) {
		clienteDocumentoCargando = true;
		toastr.info('Consultando RUC...');

		$.ajax({
			url: '../admin/classes/ConsultaApi.php',
			method: 'POST',
			data: { consultar_ruc: numero },
			dataType: 'json',
			timeout: 15000,
			success: function (data) {
				if (data && data.respuesta === 'ok') {
					aplicarRespuestaRuc(data);
					clienteDocumentoUltimoResuelto = numero;
					toastr.success('Datos de RUC cargados');
				} else {
					toastr.warning('No se encontraron datos para este RUC');
				}
			},
			error: function () {
				toastr.error('No se pudo consultar el RUC');
			},
			complete: function () {
				clienteDocumentoCargando = false;
			}
		});
	}
}
function aplicarRespuestaDni(data) {
	var nombres = normalizarTexto(data.nombres);
	var apPaterno = normalizarTexto(data.ap_paterno);
	var apMaterno = normalizarTexto(data.ap_materno);
	var nombreCompleto = [nombres, apPaterno, apMaterno].filter(Boolean).join(' ');
	setNombreCompleto(nombreCompleto);
	$("#nombres").val(nombres);
	$("#nombresHidden").val(nombres);
	$("#apellidoPaterno").val(apPaterno);
	$("#apellidoPaternoHidden").val(apPaterno);
	$("#apellidoMaterno").val(apMaterno);
	$("#apellidoMaternoHidden").val(apMaterno);
}
function setNombreCompleto(nombre) {
	$("#nombres").val(nombre || '');
	$("#nombresHidden").val(nombre || '');
}
function aplicarRespuestaRuc(data) {
	var razonSocial = normalizarTexto(data.razon_social);
	$("#razon_social").val(razonSocial);
	$("#razonSocialHidden").val(razonSocial);
	$("#nombre_comercial").val(normalizarTexto(data.nombre_comercial));
	$("#nombreComercialHidden").val(normalizarTexto(data.nombre_comercial));
	$("#condicion").val(normalizarTexto(data.condicion));
	$("#condicionHidden").val(normalizarTexto(data.condicion));
	$("#estado_ruc").val(normalizarTexto(data.estado));
	$("#estadoRucHidden").val(normalizarTexto(data.estado));
	setUbicacion({
		direccion: data.direccion,
		codigo_ubigeo: data.codigo_ubigeo,
		departamento: data.departamento,
		provincia: data.provincia,
		distrito: data.distrito,
		ciudad: data.ciudad
	});
	$("#telefono").val(normalizarTexto(data.telefono));
}
$(document).ready(function () {
	load();

	$("#numeroDocumento").on("input", function () {
		clearTimeout(clienteDocumentoTimer);
		const tipo = ($("#tipoDocumento").val() || 'DNI').toUpperCase();
		const numero = normalizarTexto($(this).val()).replace(/\s+/g, '');
		$(this).val(numero);

		limpiarCampos();

		if ((tipo === 'DNI' && numero.length === 8) || (tipo === 'RUC' && numero.length === 11)) {
			clienteDocumentoTimer = setTimeout(buscarDatosDocumento, 500);
		}
	});
	$("#numeroDocumento").on("blur", function () {
		clearTimeout(clienteDocumentoTimer);
		buscarDatosDocumento();
	});

	$("#tipoDocumento").on("change", function () {
		const tipo = $(this).val().toUpperCase();
		if (tipo === 'RUC') {
			$("#dniFields, #dniExtraFields").addClass('hidden');
			$("#rucFields").removeClass('hidden');
		} else {
			$("#dniFields, #dniExtraFields").removeClass('hidden');
			$("#rucFields").addClass('hidden');
		}
	});

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
