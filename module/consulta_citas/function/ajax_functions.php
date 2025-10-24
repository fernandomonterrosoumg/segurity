<?php

// Permitir solicitudes desde cualquier origen (para desarrollo, usa con precaución)
header("Access-Control-Allow-Origin: *");
// Permitir ciertos métodos HTTP
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include '../../../core_functions/core_functions.php';

try {
    if (isset($_GET["FUNC"])) {
        $v_Func = $_GET["FUNC"];
        if ($v_Func == "getCitas")
        {
            echo json_encode(getCitas());
        } 
        else if ($v_Func == "getCita")
        {
            echo json_encode(getCita());
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}

function getCitas() {
    $strConsulta = "
    SELECT 
        TRIM(P.PAC_NOMBRE1)||' '|| TRIM(P.PAC_NOMBRE2)||' '|| TRIM(P.PAC_APELLIDO1)||' '||TRIM(P.PAC_APELLIDO2) NOMBRE,
        C.CM_CORR,
        C.CM_ANIO,
        C.CM_FECHA_CITA,
        C.PAC_CORR,
        C.PAC_DOCNUM,
        TRIM(U.USER_NOMBRE1)||' '|| TRIM(U.USER_NOMBRE2)||' '|| TRIM(U.USER_APELLIDO1)||' '||TRIM(U.USER_APELLIDO2) DOCTOR
    FROM
        EMR.EMR_CITA_MEDICA C
    LEFT JOIN
        EMR.EMR_PACIENTE P        
    ON
        C.PAC_CORR = P.PAC_CORR
        AND C.PAC_DOCNUM = P.PAC_DOCNUM
    LEFT JOIN
        EMR.EMR_ACCE_USER U
    ON
        C.USER_ID = U.USER_ID
    ";

    $arrConsulta = _query($strConsulta);

    return $arrConsulta;
}

function getCita() {
    try {
        $respuesta = array(
            'estado' => false,
            'desc' => 'Ocurrio un error al obtener la información de la cita.'
        );

        $data = json_decode(file_get_contents('php://input'), true);

        $corr = $data['cm_corr'];
        $anio = $data['cm_anio'];

        if (isset($anio) && isset($corr)) {

            $strConsulta = "SELECT
                                TRIM(P.PAC_NOMBRE1)||' '|| TRIM(P.PAC_NOMBRE2)||' '|| TRIM(P.PAC_APELLIDO1)||' '||TRIM(P.PAC_APELLIDO2) NOMBRE,
                                C.*,
                                T.*
                            FROM
                                EMR.EMR_CITA_MEDICA C
                            LEFT JOIN
                                EMR.EMR_PACIENTE P
                            ON
                                C.PAC_CORR = P.PAC_CORR
                                AND C.PAC_DOCNUM = P.PAC_DOCNUM
                            LEFT JOIN
                                EMR.EMR_TIPO_CITA T
                            ON
                                C.TC_ID = T.TC_ID
                            WHERE
                                C.CM_CORR = :corr
                                AND C.CM_ANIO = :anio
                            ";

            $params = array(
                ':corr' => $corr,
                ':anio' => $anio,
            );

            $result = _queryBind($strConsulta, $params);            

            if (empty($result)) {
                $respuesta['estado'] = false;
                throw new Exception("No se encontró la cita: " . $corr . "-" . $anio);
            }

            $respuesta['estado'] = true;
            $respuesta['desc'] = $result;
            return $respuesta;

        } else {
            $respuesta['estado'] = false;
            throw new Exception("No se especificarón los parametros necesarios para esta solicitud.");
        }
    } catch (Exception $e) {
        $respuesta['desc'] = $e->getMessage();
        return $respuesta;
    }

    return $respuesta;
}