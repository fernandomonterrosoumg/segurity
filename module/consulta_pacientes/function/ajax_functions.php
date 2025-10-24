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
        if ($v_Func == "getPacientes")
        {
            echo json_encode(getPacientes());
        } 
        else if ($v_Func == "getPaciente")
        {
            echo json_encode(getPaciente());
        }
        else if ($v_Func == "updPaciente")
        {
            echo json_encode(updPaciente());
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}

function getPacientes() {
    $strConsulta = "
    SELECT 
        P.PAC_CORR,
        P.PAC_DOCNUM,
        TRIM(P.PAC_NOMBRE1)||' '|| TRIM(P.PAC_NOMBRE2)||' '|| TRIM(P.PAC_APELLIDO1)||' '||TRIM(P.PAC_APELLIDO2) NOMBRE,
        P.PAC_CORREO,
        P.PAC_TELEFONO_EMG,
        S.TS_DESC||S.TS_FACTOR_RH TIPO_SANGRE
    FROM
        EMR.EMR_PACIENTE P
    INNER JOIN
        EMR.EMR_DATOS_CLINICOS D
    ON
        P.PAC_CORR = D.PAC_CORR
        AND P.PAC_DOCNUM = D.PAC_DOCNUM
    LEFT JOIN
        EMR.EMR_TIPO_SANGRE S
    ON
        D.ID_TS = S.TS_ID
    ";

    $arrConsulta = _query($strConsulta);

    return $arrConsulta;
}

function getPaciente() {
    try {
        $respuesta = array(
            'estado' => false,
            'desc' => 'Ocurrio un error al obtener la información del usuario.'
        );

        $data = json_decode(file_get_contents('php://input'), true);

        $doc = $data['doc_id'];
        $corr = $data['pac_corr'];

        if (isset($doc) && isset($corr)) {            

            $strConsulta = "SELECT 
                                P.PAC_CORR,
                                P.PAC_DOCNUM,
                                TRIM(P.PAC_NOMBRE1)||' '|| TRIM(P.PAC_NOMBRE2)||' '|| TRIM(P.PAC_APELLIDO1)||' '||TRIM(P.PAC_APELLIDO2) NOMBRE,
                                P.PAC_NOMBRE1,
                                P.PAC_NOMBRE2,
                                P.PAC_NOMBRE3,
                                P.PAC_APELLIDO1,
                                P.PAC_APELLIDO2,
                                P.PAC_APELLIDOC,
                                P.PAC_FOTO,
                                P.PAC_DIRECCION,
                                P.PAC_TELEFONO,
                                P.PAC_CORREO,
                                P.PAC_TELEFONO_EMG,
                                TO_CHAR(P.PAC_FEC_NAC,'DD/MM/YYYY') PAC_FEC_NAC,
                                P.PAC_OCUPACION,
                                P.NAC_ID,
                                P.PAC_FEC_REGISTRO,
                                P.EC_ID,
                                P.PAC_ESTADO_SN,
                                D.DC_GENERO,
                                D.DC_PESOKG_ACTUAL,
                                D.DC_PESOLB_ACTUAL,
                                D.DC_ALTURACM_ACTUAL,
                                D.DC_ULT_CITA_CONSUL,
                                D.DC_ULT_MED_GLUCOSAMGDL,
                                D.DC_ULT_MED_GLUCOSAMGDL,
                                D.DC_ULT_MED_PA_MMHG,
                                D.DC_ULT_MED_FC_LPM,
                                S.TS_DESC,
                                S.TS_FACTOR_RH,
                                S.TS_DESC||S.TS_FACTOR_RH TIPO_SANGRE,
                                C.EC_DESC,
                                INITCAP(N.NAC_DESC) NAC_DESC,
                                TRUNC((SYSDATE - P.PAC_FEC_NAC) / 365) AS EDAD
                            FROM
                                EMR.EMR_PACIENTE P
                            INNER JOIN
                                EMR.EMR_DATOS_CLINICOS D
                            ON
                                P.PAC_CORR = D.PAC_CORR
                                AND P.PAC_DOCNUM = D.PAC_DOCNUM
                            LEFT JOIN
                                EMR.EMR_ESTADO_CIVIL C
                            ON
                                P.EC_ID = C.EC_ID
                            LEFT JOIN
                                EMR.EMR_NACIONALIDAD N
                            ON
                                P.NAC_ID = N.NAC_ID
                            LEFT JOIN
                                EMR.EMR_TIPO_SANGRE S
                            ON
                                D.ID_TS = S.TS_ID
                            WHERE
                                P.PAC_DOCNUM = :doc
                                AND P.PAC_CORR = :corr
                            ";

            $params = array(
                ':doc' => $doc,
                ':corr' => $corr,
            );

            $result = _queryBind($strConsulta, $params);            

            if (empty($result)) {
                $respuesta['estado'] = false;
                throw new Exception("No se encontró el paciente: " . $corr . "-" . $doc);
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

function updPaciente() {
    try {
        $conexion = _connectDB();
        $respuesta = array(
            'estado' => false,
            'desc' => 'Ocurrio un error al actualizar la información del usuario.'
        );

        $data = json_decode(file_get_contents('php://input'), true);

        $paciente = $data['paciente'];

        if (isset($paciente)) {
            
            $strUpdPaciente = "UPDATE EMR.EMR_PACIENTE
                                SET PAC_NOMBRE1 = :pac_nombre1,
                                    PAC_NOMBRE2 = :pac_nombre2,
                                    PAC_NOMBRE3 = :pac_nombre3,
                                    PAC_APELLIDO1 = :pac_apellido1,
                                    PAC_APELLIDO2 = :pac_apellido2,
                                    PAC_APELLIDOC = :pac_apellidoc,
                                    PAC_FOTO = :pac_foto,
                                    PAC_OCUPACION = :pac_ocupacion,
                                    EC_ID = :ec_id,
                                    PAC_TELEFONO = :pac_telefono,
                                    PAC_CORREO = :pac_correo,
                                    PAC_TELEFONO_EMG = :pac_telefono_emg,
                                    PAC_DIRECCION = :pac_direccion
                                WHERE PAC_DOCNUM = :pac_docnum
                                AND PAC_CORR = :pac_corr

                            ";

            $params = array(
                ':pac_nombre1' => $paciente['PAC_NOMBRE1'],
                ':pac_nombre2' => $paciente['PAC_NOMBRE2'],
                ':pac_nombre3' => $paciente['PAC_NOMBRE3'],
                ':pac_apellido1' => $paciente['PAC_APELLIDO1'],
                ':pac_apellido2' => $paciente['PAC_APELLIDO2'],
                ':pac_apellidoc' => $paciente['PAC_APELLIDOC'],
                ':pac_foto' => $paciente['PAC_FOTO'],
                ':pac_ocupacion' => $paciente['PAC_OCUPACION'],
                ':ec_id' => $paciente['EC_ID'],
                ':pac_telefono' => $paciente['PAC_TELEFONO'],
                ':pac_correo' => $paciente['PAC_CORREO'],
                ':pac_telefono_emg' => $paciente['PAC_TELEFONO_EMG'],
                ':pac_direccion' => $paciente['PAC_DIRECCION'],
                ':pac_docnum'  => $paciente['PAC_DOCNUM'],
                ':pac_corr' => $paciente['PAC_CORR'],
            );

            $result = _queryBindNoCommit($conexion,$params,$strUpdPaciente);            

            if (!$result) {
                $respuesta['estado'] = false;
                throw new Exception("Ocurrio un error al acutalizar la información personal del paciente");
            }

            $pesoLB = $paciente['DC_PESOLB_ACTUAL'];
            $pesoKG = number_format($pesoLB / 2.205, 2, '.', '');

            $strUpdPaciente = "UPDATE EMR.EMR_DATOS_CLINICOS
                                SET DC_PESOLB_ACTUAL = $pesoLB,
                                    DC_PESOKG_ACTUAL = $pesoKG,
                                    DC_ALTURACM_ACTUAL = :alturacm_actual,
                                    DC_ULT_MED_GLUCOSAMGDL = :med_glucosamgdl,
                                    DC_ULT_MED_PA_MMHG = :med_pa,
                                    DC_ULT_MED_FC_LPM = :med_fc
                                WHERE PAC_DOCNUM = :pac_docnum
                                AND PAC_CORR = :pac_corr
                            ";
            
            $params = array(
                ':alturacm_actual' => $paciente['DC_ALTURACM_ACTUAL'],
                ':med_glucosamgdl' => $paciente['DC_ULT_MED_GLUCOSAMGDL'],
                ':med_pa' => $paciente['DC_ULT_MED_PA_MMHG'],
                ':med_fc' => $paciente['DC_ULT_MED_FC_LPM'],
                ':pac_docnum'  => $paciente['PAC_DOCNUM'],
                ':pac_corr' => $paciente['PAC_CORR'],                
            );

            $resul = _queryBindNoCommit($conexion, $params, $strUpdPaciente);

            if (!$resul) {
                $respuesta['estado'] = false;
                throw new Exception("Ocurrio un error al acutalizar los datos clínicos de paciente");
            }

            _queryCommit($conexion);
            oci_close($conexion);

            $respuesta['estado'] = true;
            $respuesta['desc'] = "Paciente actualizado exitosamente";

        } else {
            $respuesta['estado'] = false;
            throw new Exception("No se especificarón los parametros necesarios para esta solicitud.");
        }
    } catch (Exception $e) {
        http_response_code(500);
        _queryRollback($conexion);
        oci_close($conexion);
        $respuesta['desc'] = $e->getMessage();
        return $respuesta;
    }

    return $respuesta;
}