<?php

// Permitir solicitudes desde cualquier origen (para desarrollo, usa con precaución)
header("Access-Control-Allow-Origin: *");
// Permitir ciertos métodos HTTP
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// Permitir ciertos encabezados
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include '../../../core_functions/core_functions.php';

session_start();

try {
    if (isset($_GET["FUNC"])) {
        $v_Func = $_GET["FUNC"];
        if ($v_Func == "getPaciente")
        {
            echo json_encode(getPaciente());
        } 
        else if ($v_Func == "getTipoCita")
        {
            echo json_encode(getTipoCita());
        } 
        else if ($v_Func == "guardarCita")
        {
            echo json_encode(guardarCita());
        } 
        /*else if ($v_Func == "getKeys")
        {
            echo json_encode(getKeys());
        } 
        else if ($v_Func == "guardarPaciente")
        {
            echo json_encode(guardarPaciente());
        }*/
    }
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}

function getPaciente() {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $pac_corr = $data['pac_corr'];
        $pac_docnum = $data['pac_docnum'];

        //Consulta si ya existe el paciente
        $strConsulta = "SELECT 
                            TRIM(PAC_NOMBRE1)||' '|| TRIM(PAC_NOMBRE2)||' '|| TRIM(PAC_APELLIDO1)||' '||TRIM(PAC_APELLIDO2) NOMBRE,
                            TRIM(PAC_NOMBRE1) PAC_NOMBRE1,
                            TRIM(PAC_NOMBRE2) PAC_NOMBRE2,
                            TRIM(PAC_NOMBRE3) PAC_NOMBRE3,
                            TRIM(PAC_APELLIDO1) PAC_APELLIDO1,
                            TRIM(PAC_APELLIDO2) PAC_APELLIDO2,
                            TRIM(PAC_APELLIDOC) PAC_APELLIDOC,
                            PAC_FOTO,
                            PAC_DIRECCION,
                            PAC_TELEFONO,
                            PAC_CORREO,
                            PAC_TELEFONO_EMG,
                            PAC_FEC_NAC,
                            PAC_OCUPACION,
                            NAC_ID,
                            PAC_FEC_REGISTRO,
                            EC_ID,
                            PAC_ESTADO_SN
                        FROM 
                            EMR.EMR_PACIENTE 
                        WHERE 
                            PAC_DOCNUM = :docId
                            AND PAC_CORR = :corr";

        // Parámetros de la consulta
        $params = array(
            ':docId' => $pac_docnum,
            ':corr' => $pac_corr,
        );

        // Ejecuta la consulta
        $resultado = _queryBind($strConsulta, $params);

        // Verifica si se obtuvo algún resultado
        if (isset($resultado[0])) {
            $respuesta = $resultado[0];
        } else {
            throw new Exception("No se encontro ningún paciente con ese número de identificación o correlativo.");
        }

        return array(
            'estado' => true,
            'desc' => $respuesta 
        );


    } catch (Exception $e) {
        http_response_code(500);
        return array(
            'estado' => false,
            'desc' => $e->getMessage(),
        );
    }
}

function getTipoCita() {
    $strConsulta = "SELECT * FROM EMR.EMR_TIPO_CITA";

    $arrConsulta = _query($strConsulta);

    return $arrConsulta;
}

function guardarCita() {
    //Crea una conexion a la base de datos.
    $conexion = _connectDB();
    try {
        //Obtiene los datos y decodifica el JSON
        $data = json_decode(file_get_contents('php://input'), true);

        $cita = $data['cita'];
        $datClinico = $data['datClinico'];

        /*VALIDA SI ES UNA CITA DE SEGUIMIENTO Y SI LA CITA PREVIA EXISTE*/
        if (!empty($cita['anio_padre']) && !empty($cita['corr_padre'])) {
            //Consulta si ya existe el paciente
            $strConsulta = "SELECT 1 FROM EMR.EMR_CITA_MEDICA WHERE CM_CORR = :cm_corr AND CM_ANIO = :cm_anio";

            // Parámetros de la consulta
            $params = array(
                ':cm_corr' => $cita['corr_padre'],
                ':cm_anio' => $cita['anio_padre'],
            );

            $resul =  _queryBind($strConsulta,$params);

            if(empty($resul)){
                return array(
                    'estado' => false,
                    'desc' => 'No existe ninguna cita previa con estos valores: '. $cita['corr_padre']. '-' .$cita['anio_padre'] .'.',
                );
            }
        }

        /*TRANSACCION PARA INSERTAR LA INFORMACIÓN DE LA CITA*/

        //INSERCIÓN A EMR_CITA_MEDICA
        $strInsPaciente = 
            "INSERT INTO EMR.EMR_CITA_MEDICA 
            (
            CM_FECHA_CITA,
            CM_MOTIVO_CITA,
            CM_DIAGNOSTICO,
            CM_TRATAMIENTO,
            CM_OBSEVACIONES,
            CM_CITA_SEGUIMIENTO_SN,
            CM_ARCH_RECETA,
            CM_CORR_PADRE,
            CM_ANIO_PADRE,
            PAC_CORR,
            PAC_DOCNUM,
            USER_ID,
            TC_ID
            )
            VALUES
            (
            SYSDATE,
            :cm_motivo_cita,
            :cm_diagnostico,
            :cm_tratamiento,
            :cm_observaciones,
            :cm_seguimiento,
            :cm_receta,
            :cm_corr_padre,
            :cm_anio_padre,
            :pac_corr,
            :pac_docnum,
            :user_id,
            :tc_id
            )";


        if(!$cita['arch_receta']) {
            $cita['arch_receta'] = null;
        }

        // Parámetros de la consulta
        $params = array(
            ':cm_motivo_cita' => $cita['motivo'],
            ':cm_diagnostico' => $cita['diagnostico'],
            ':cm_tratamiento' => $cita['tratamiento'],
            ':cm_observaciones' => $cita['observaciones'],
            ':cm_seguimiento' => $cita['cita_seguimiento'],
            ':cm_receta' => $cita['arch_receta'],
            ':cm_corr_padre' => $cita['corr_padre'],
            ':cm_anio_padre' => $cita['anio_padre'],
            ':pac_corr' => $cita['pac_corr'],
            ':pac_docnum' => $cita['pac_docnum'],
            ':user_id' => $_SESSION['user_id'],
            ':tc_id' => $cita['tc_id'],
        );

        // Ejecuta la consulta sin commit
        $resul = _queryBindNoCommit($conexion, $params, $strInsPaciente);

        if (!$resul) {
            throw new Exception("Error inesperado al intentar guardar la información de la cita médica.");
        }

        //ACTUALIZCIÓN A EMR_DATOS_CLINICOS

        //if (!empty($datClinico['pesoLB']) || !empty($datClinico['altura']) || !empty($datClinico['presionArt']) || !empty($datClinico['freqCardiaca']) || !empty($datClinico['medGlucosa'])) {
            
            $actualizar = "";
            $params = array();

            if (!empty($datClinico['pesoLB'])) {
                //Se obtienen y comvierten los pesos 
                $pesoLB = number_format((float)$datClinico['pesoLB'], 2, '.', '');
                $pesoKG = number_format($pesoLB / 2.205, 2, '.', '');

                // No se bindea por temas de incompatibilidad con tipos float.
                $actualizar .= "DC_PESOLB_ACTUAL = $pesoLB, DC_PESOKG_ACTUAL = $pesoKG,";
            }

            if (!empty($datClinico['altura'])) {
                $actualizar .= "DC_ALTURACM_ACTUAL = :dc_alturacm_actual,";
                $params[':dc_alturacm_actual'] = $datClinico['altura'];
            }

            if (!empty($datClinico['presionArt'])) {
                $actualizar .= "DC_ULT_MED_PA_MMHG = :dc_ult_med_pa_mmhg,";
                $params[':dc_ult_med_pa_mmhg'] = $datClinico['presionArt'];
            }
            
            if (!empty($datClinico['freqCardiaca'])) {
                $actualizar .= "DC_ULT_MED_FC_LPM = :dc_ult_med_fc_lpm,";
                $params[':dc_ult_med_fc_lpm'] = $datClinico['freqCardiaca'];
            }

            if (!empty($datClinico['medGlucosa'])) {
                $actualizar .= "DC_ULT_MED_GLUCOSAMGDL = :dc_ult_med_glucosamgdl,";
                $params[':dc_ult_med_glucosamgdl'] = $datClinico['medGlucosa'];
            }
            
            //$actualizar = rtrim($actualizar, ',');

            $strInsDatosClinicos = 
                "UPDATE EMR.EMR_DATOS_CLINICOS
                SET
                    $actualizar
                    DC_ULT_CITA_CONSUL = SYSDATE
                WHERE
                    PAC_CORR = :pac_corr
                    AND PAC_DOCNUM = :pac_docnum";

            // Parámetros de la consulta
            $params[':pac_corr'] = $cita['pac_corr'];

            $params[':pac_docnum'] = $cita['pac_docnum'];

            // Ejecuta la consulta sin commit
            $resul = _queryBindNoCommit($conexion, $params, $strInsDatosClinicos);

            if (!$resul) {
                throw new Exception("Error inesperado al intentar actualizar la información biométrica y vital del paciente.");
            }
        //}

        _queryCommit($conexion);
        oci_close($conexion);

    } catch (Exception $e) {
        http_response_code(500);
        _queryRollback($conexion);
        oci_close($conexion);
        return array(
            'estado' => false,
            'desc' => $e->getMessage(),
        );
    }

    return array(
        'estado' => true,
        'desc' => 'Cita ingresada correctamente.',
    );
}