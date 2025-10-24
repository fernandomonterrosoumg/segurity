<?php
session_start();
include '../../../core_functions/core_functions.php';
try {
    if (isset($_GET["FUNC"])) {
        $v_Func = $_GET["FUNC"];
        if ($v_Func == "getModulos") {
            echo json_encode(getModulos());
        }
        if ($v_Func == "getTest") {
            echo json_encode(_connectDB());
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}


function getModulos()
{
    try {
        $respuesta = array(
            'estado' => 3,
            'desc' => 'Ocurrio un error al obtener los modulos del usuario.'
        );
        if (isset($_SESSION['rol_id'])) {
            $rol_id = $_SESSION['rol_id'];
            

            $strConsulta = "SELECT
                                M.MDL_NOM,
                                M.MDL_RUTA,
                                M.MDL_IMG
                            FROM
                                EMR.EMR_ACCE_MODULOROL R
                            INNER JOIN
                                EMR.EMR_ACCE_MODULO M
                            ON
                                R.MDL_ID = M.MDL_ID
                            WHERE
                                ROL_ID = :rol_id
                            ORDER BY
                                M.MDL_ID ASC
                            ";

            $params = array(
                ':rol_id' => $rol_id,
            );

            $result = _queryBind($strConsulta, $params);            

            if (empty($result)) {
                $respuesta['estado'] = 2;
                throw new Exception("El usuario aún no tiene modulos asignados.");
            }

            $respuesta['estado'] = 1;
            $respuesta['desc'] = $result;
            return $respuesta;

        } else {
            $respuesta['estado'] = 2;
            throw new Exception("El usuario no tiene un rol establecido.");
        }
    } catch (Exception $e) {
        $respuesta['desc'] = $e->getMessage();
        return $respuesta;
    }

    return $respuesta;
}