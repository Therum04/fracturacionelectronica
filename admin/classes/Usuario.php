<?php


class Usuario
{

	private $con;

	function __construct()
	{
		include_once("Database.php");
		$db = new Database();
		$this->con = $db->connect();
	}

	public function addRegistro($idusuario, $nombres, $apellidos, $email, $clave, $idrol, $lugar_entraga)
	{

		if ($idusuario == 0) {
			$q = $this->con->query("SELECT * FROM usuario WHERE email = '$email' LIMIT 1");
			if ($q->num_rows > 0) {
				return ['status' => 303, 'message' => 'ya existe un registro'];
			}
			$hash = password_hash($clave, PASSWORD_DEFAULT);
			$stmt =  $this->con->prepare("INSERT INTO usuario 
            (nombres, apellidos, email, clave, idrol, lugar_entraga, estado)
            VALUES (?, ?, ?, ?, ?, ?, 1)");
			$stmt->bind_param(
				"ssssis",
				$nombres,
				$apellidos,
				$email,
				$hash,
				$idrol,
				$lugar_entraga
			);
			if ($stmt->execute()) {

				return ['status' => 202, 'message' => 'Cuenta creada correctamente. A'];
			} else {
				return ['status' => 303, 'message' => 'Error al registrar usuario'];
			}
		} else {

			$q = $this->con->query("UPDATE usuario
			 SET nombres= '$nombres',
			 apellidos= '$apellidos',
			 email= '$email',
			 lugar_entraga= '$lugar_entraga'
			 WHERE idusuario = '$idusuario'");
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
			$q = $this->con->query("SELECT * FROM pedido WHERE idusuario = '$cid' LIMIT 1");
			if ($q->num_rows > 0) {
				return ['status' => 303, 'message' => 'No se puede eliminar el registro existe una relación con pedidos'];
			}
			$q = $this->con->query("DELETE FROM usuario WHERE idusuario = '$cid'")  or die($this->con->error);
			if ($q) {
				return ['status' => 202, 'message' => 'El registro se elimino correctamente'];
			} else {
				return ['status' => 202, 'message' => 'No se ha podido eliminar el registro'];
			}
		} else {
			return ['status' => 303, 'message' => 'ID de area inválido'];
		}
	}

	public function getRoles()
	{
		$roles = [];
		$q = $this->con->query("SELECT r.idrol, r.descripcion FROM rol r ");
		if ($q->num_rows > 0) {
			while ($row = $q->fetch_assoc()) {
				$roles[] = $row;
			}
			$_DATA['roles'] = $roles;
		}
		return ['status' => 202, 'message' => $_DATA];
	}
	public function updateClave($idusuario, $clave)
	{

		$sql = "UPDATE usuario SET clave=? WHERE idusuario=?";
		$stmt = $this->con->prepare($sql);
		$stmt->bind_param("si", $clave, $idusuario);

		if ($stmt->execute()) {
			return [
				"status" => 202,
				"message" => "Contraseña actualizada correctamente"
			];
		} else {
			return [
				"status" => 303,
				"message" => "Error al actualizar la contraseña"
			];
		}
	}
}

if (isset($_POST['add_update'])) {
	$idusuario = $_POST['idusuario'];
	$nombres = $_POST['nombres'];
	$apellidos = $_POST['apellidos'];
	$email = $_POST['email'];
	$clave = $_POST['clave'];
	$idrol = $_POST['idrol'];
	$lugar_entraga = $_POST['lugar_entraga'];
	$p = new Usuario();
	echo json_encode($p->addRegistro($idusuario, $nombres, $apellidos, $email, $clave, $idrol, $lugar_entraga));
}



if (isset($_POST['eliminar_registro'])) {
	if (!empty($_POST['cid'])) {
		$p = new Usuario();
		echo json_encode($p->deleteRegistro($_POST['cid']));
		exit();
	} else {
		echo json_encode(['status' => 303, 'message' => 'ID de usuario inválido']);
		exit();
	}
}

if (isset($_POST['GET_ROLES'])) {
	$p = new Usuario();
	echo json_encode($p->getRoles());
	exit();
}
if (isset($_POST['update_clave'])) {

	$idusuario  = $_POST['id'];
	$newclave   = $_POST['newclave'];
	$confclave  = $_POST['confclave'];

	// 🔴 Validar que sean iguales
	if ($newclave !== $confclave) {
		echo json_encode([
			"status" => 303,
			"message" => "Las contraseñas no coinciden"
		]);
		exit;
	}

	// 🔐 Encriptar contraseña
	$hash = password_hash($newclave, PASSWORD_BCRYPT);

	$p = new Usuario();
	echo json_encode($p->updateClave($idusuario, $hash));
}
