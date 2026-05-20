<?php

class Cliente
{
    private $con;

    function __construct()
    {
        include_once("Database.php");
        $db = new Database();
        $this->con = $db->connect();
    }

    public function getAllClientes()
    {
        $data = [];
        $sql = "SELECT id_cliente, nombre_razon_social FROM cliente";
        $q = $this->con->query($sql);
        if ($q && $q->num_rows > 0) {
            while ($row = $q->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }
    public function addRegistro(
        $id_cliente,
        $id_tipo_doc,
        $numero_doc,
        $nombre_razon_social,
        $direccion,
        $correo,
        $telefono
    ) {

        if ($id_cliente == 0) {
            $q = $this->con->query("SELECT * FROM cliente WHERE numero_doc = '$numero_doc' and  id_tipo_doc = '$id_tipo_doc' LIMIT 1");
            if ($q->num_rows > 0) {
                return ['status' => 303, 'message' => 'ya existe un registro'];
            }



            $stmt =  $this->con->prepare("INSERT INTO cliente 
            (id_tipo_doc, numero_doc, nombre_razon_social, direccion, correo, telefono)
            VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param(
                "isssss",
                $id_tipo_doc,
                $numero_doc,
                $nombre_razon_social,
                $direccion,
                $correo,
                $telefono
            );
            if ($stmt->execute()) {

                return ['status' => 202, 'message' => 'Se registró correctamente.'];
            } else {
                return ['status' => 303, 'message' => 'Error al registrar producto'];
            }
        } else {
            $q = $this->con->query("UPDATE cliente
			 SET id_tipo_doc= '$id_tipo_doc',
			 numero_doc= '$numero_doc',
			 nombre_razon_social= '$nombre_razon_social',
			 direccion= '$direccion',
             correo= '$correo',
             telefono= '$telefono'
			 WHERE id_cliente = '$id_cliente'");
            if ($q) {
                return ['status' => 202, 'message' => 'Registro modificado correctamente'];
            } else {
                return ['status' => 303, 'message' => 'No se ha podido modificar el registro'];
            }
        }
    }

    public function deleteRegistro($cid = null)
    {
        if ($cid != null) {
            $q = $this->con->query("DELETE FROM cliente WHERE id_cliente = '$cid'") or die($this->con->error);
            if ($q) {
                return ['status' => 202, 'message' => 'El registro se eliminó correctamente'];
            } else {
                return ['status' => 303, 'message' => 'No se ha podido eliminar el registro'];
            }
        } else {
            return ['status' => 303, 'message' => 'ID de cliente inválido'];
        }
    }
}

if (isset($_POST['eliminar_registro'])) {
    if (!empty($_POST['cid'])) {
        $p = new Cliente();
        echo json_encode($p->deleteRegistro($_POST['cid']));
        exit();
    }
}


if (isset($_POST['add_update'])) {
    $id_cliente = $_POST['id_cliente'];
    $id_tipo_doc = $_POST['id_tipo_doc'];
    $numero_doc = $_POST['numero_doc'];
    $nombre_razon_social = $_POST['nombre_razon_social'];
    $direccion = $_POST['direccion'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];
    $p = new Cliente();
    echo json_encode($p->addRegistro(
        $id_cliente,
        $id_tipo_doc,
        $numero_doc,
        $nombre_razon_social,
        $direccion,
        $correo,
        $telefono
    ));
}
