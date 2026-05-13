<?php


class Producto
{

	private $con;

	function __construct()
	{
		include_once("Database.php");
		$db = new Database();
		$this->con = $db->connect();
	}

	public function addRegistro($id_producto, $descripcion, $id_unidad, $precio_unitario, $id_impuesto)
	{

		if ($id_producto == 0) {
			$q = $this->con->query("SELECT * FROM producto_servicio WHERE descripcion = '$descripcion' and  id_unidad = '$id_unidad' LIMIT 1");
			if ($q->num_rows > 0) {
				return ['status' => 303, 'message' => 'ya existe un registro'];
			}
			
			$res_code = $this->con->query("SELECT count(id_producto) as last_id FROM producto_servicio");
			$row_code = $res_code->fetch_assoc();
			$next_id = ($row_code['last_id']) ? $row_code['last_id'] + 1 : 1;

			// Formato: M0001
			$codigo_barra = "PROD" . str_pad($next_id, 3, "0", STR_PAD_LEFT);

			$stmt =  $this->con->prepare("INSERT INTO producto_servicio 
            (codigo_producto, descripcion, id_unidad, precio_unitario, id_impuesto, activo)
            VALUES (?, ?, ?, ?, ?, 1)");
			$stmt->bind_param(
				"ssidi",
				$codigo_barra,
				$descripcion,
				$id_unidad,
				$precio_unitario,
				$id_impuesto
			);
			if ($stmt->execute()) {

				return ['status' => 202, 'message' => 'Se registró correctamente.'];
			} else {
				return ['status' => 303, 'message' => 'Error al registrar producto'];
			}
		} else {

			$q = $this->con->query("UPDATE producto_servicio
			 SET descripcion= '$descripcion',
			 id_unidad= '$id_unidad',
			 precio_unitario= '$precio_unitario',
			 id_impuesto= '$id_impuesto'
			 WHERE id_producto = '$id_producto'");
			if ($q) {
				return ['status' => 202, 'message' => 'Registro modificado correctamente'];
			} else {
				return ['status' => 303, 'message' => 'No se podido modificar el registro'];
			}
		}
	}


	public function deleteRegistro($cid = null)
	{
		if ($cid != null) {
			
			$q = $this->con->query("DELETE FROM producto_servicio WHERE id_producto = '$cid'")  or die($this->con->error);
			if ($q) {
				return ['status' => 202, 'message' => 'El registro se elimino correctamente'];
			} else {
				return ['status' => 202, 'message' => 'No se ha podido eliminar el registro'];
			}
		} else {
			return ['status' => 303, 'message' => 'ID de area inválido'];
		}
	}

	

}

if (isset($_POST['add_update'])) {
	$id_producto = $_POST['id_producto'];
	$descripcion = $_POST['descripcion'];
	$id_unidad = $_POST['id_unidad'];
	$precio_unitario = $_POST['precio_unitario'];
	$id_impuesto = $_POST['id_impuesto'];
	

	$p = new Producto();
	echo json_encode($p->addRegistro($id_producto, $descripcion, $id_unidad, $precio_unitario, $id_impuesto));
}



if (isset($_POST['eliminar_registro'])) {
	if (!empty($_POST['cid'])) {
		$p = new Producto();
		echo json_encode($p->deleteRegistro($_POST['cid']));
		exit();
	} else {
		echo json_encode(['status' => 303, 'message' => 'ID de usuario inválido']);
		exit();
	}
}

