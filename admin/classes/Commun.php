<?php
class Commun
{
    private $con;
    function __construct()
    {
        include_once("Database.php");
        $db = new Database();
        $this->con = $db->connect();
    }

    public function getUnidadMedida()
    {
        $enumerado = [];
        $q = $this->con->query("SELECT u.id_unidad, u.nombre FROM unidad_medida u ");
        if ($q->num_rows > 0) {
            while ($row = $q->fetch_assoc()) {
                $enumerado[] = $row;
            }
            $_DATA['enumerado'] = $enumerado;
        }
        return ['status' => 202, 'message' => $_DATA];
    }

    public function getTipoImpuesto()
    {
        $enumerado = [];
        $q = $this->con->query("SELECT i.id_impuesto, i.nombre FROM tipo_impuesto i ");
        if ($q->num_rows > 0) {
            while ($row = $q->fetch_assoc()) {
                $enumerado[] = $row;
            }
            $_DATA['enumerado'] = $enumerado;
        }
        return ['status' => 202, 'message' => $_DATA];
    }
}

if (isset($_POST['GET_UNIDADMEDIDA'])) {
    $p = new Commun();
    echo json_encode($p->getUnidadMedida());
    exit();
}
if (isset($_POST['GET_TIPOIMPUESTO'])) {
    $p = new Commun();
    echo json_encode($p->getTipoImpuesto());
    exit();
}
