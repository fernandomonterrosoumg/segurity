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
        if ($v_Func == "getEstadoCivil")
        {
            echo json_encode(getEstadoCivil());
        } 
        else if ($v_Func == "getTipoSangre")
        {
            echo json_encode(getTipoSangre());
        } 
        else if ($v_Func == "getNacionalidad")
        {
            echo json_encode(getNacionalidad());
        } 
        else if ($v_Func == "getKeys")
        {
            echo json_encode(getKeys());
        } 
        else if ($v_Func == "guardarPaciente")
        {
            echo json_encode(guardarPaciente());
        }
    }
} catch (Exception $e) {
    http_response_code(500);
    echo $e->getMessage();
}

function getEstadoCivil(){
    $strConsulta = "SELECT * FROM EMR.EMR_ESTADO_CIVIL";

    $arrConsulta = _query($strConsulta);

    return $arrConsulta;
}

function getTipoSangre() {
    $strConsulta = "SELECT * FROM EMR.EMR_TIPO_SANGRE";

    $arrConsulta = _query($strConsulta);

    return $arrConsulta;
}

function getNacionalidad() {
    $strConsulta = "SELECT * FROM EMR.EMR_NACIONALIDAD";

    $arrConsulta = _query($strConsulta);

    return $arrConsulta;
}

function getKeys(){
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $paciente = $data['paciente'];

        //Consulta si ya existe el paciente
        $strConsulta = "SELECT NVL(MAX(PAC_CORR),0) + 1 CORR FROM EMR.EMR_PACIENTE WHERE PAC_DOCNUM = :docId";

        // Parámetros de la consulta
        $params = array(
            ':docId' => $paciente['docId'],
        );

        // Ejecuta la consulta
        $resultado = _queryBind($strConsulta, $params);

        // Verifica si se obtuvo algún resultado
        if (isset($resultado[0]['CORR'])) {
            $correlativo = $resultado[0]['CORR'];
        } else {
            $correlativo = 1; // Si no hay resultado, inicia con 1 o cualquier valor por defecto
        }

        return array(
            'estado' => true,
            'corr' => $correlativo 
        );


    } catch (Exception $e) {
        http_response_code(500);
        return array(
            'estado' => false,
            'desc' => $e->getMessage(),
        );
    }
}

function guardarPaciente() {

    //Crea una conexion a la base de datos.
    $conexion = _connectDB();
    try {
        //Obtiene los datos y decodifica el JSON
        $data = json_decode(file_get_contents('php://input'), true);

        $paciente = $data['paciente'];
        $datClinico = $data['datClinico'];

        /*VALIDA SI EL PACIENTE YA EXISTE*/
        
        //Consulta si ya existe el paciente
        $strConsulta = "SELECT 1 FROM EMR.EMR_PACIENTE WHERE PAC_DOCNUM = :docId AND NAC_ID = :nacionalidadId AND PAC_ESTADO_SN = 'S'";

        // Parámetros de la consulta
        $params = array(
            ':docId' => $paciente['docId'],
            ':nacionalidadId' => $paciente['nacionalidadId'],
        );

        $resul =  _queryBind($strConsulta,$params);

        if(!empty($resul)){
            return array(
                'estado' => false,
                'desc' => 'El paciente ' . $paciente['nombre1'] . ' ' . $paciente['apellido1'] . ' ya existe.',
            );
        }

        /*TRANSACCION PARA INSERTAR LA INFORMACIÓN DEL PACIENTE*/

        //INSERCIÓN A EMR_PACIENTE
        $strInsPaciente = 
            "INSERT INTO EMR.EMR_PACIENTE 
            (
            PAC_CORR,
            PAC_DOCNUM,
            PAC_NOMBRE1,
            PAC_NOMBRE2,
            PAC_NOMBRE3,
            PAC_APELLIDO1,
            PAC_APELLIDO2,
            PAC_APELLIDOC,
            PAC_FOTO,
            PAC_DIRECCION,
            PAC_TELEFONO,
            PAC_CORREO,
            PAC_TELEFONO_EMG,
            PAC_FEC_NAC,
            PAC_OCUPACION,
            NAC_ID,
            PAC_FEC_REGISTRO,
            EC_ID
            )
            VALUES
            (
            :pac_corr,
            :pac_docnum,
            :pac_nombre1,
            :pac_nombre2,
            :pac_nombre3,
            :pac_apellido1,
            :pac_apellido2,
            :pac_apellidoc,
            :pac_foto,
            :pac_direccion,
            :pac_telefono,
            :pac_correo,
            :pac_telefono_emg,
            to_date(:pac_fec_nac,'YYYY-MM-DD'),
            :pac_ocupacion,
            :nac_id,
            SYSDATE,
            :ec_id
            )";


        if(!$paciente['foto']) {
            $paciente['foto'] = null;
        }

        // Parámetros de la consulta
        $params = array(
            ':pac_corr' => $paciente['correlativo'],
            ':pac_docnum' => $paciente['docId'],
            ':pac_nombre1' => $paciente['nombre1'],
            ':pac_nombre2' => $paciente['nombre2'],
            ':pac_nombre3' => $paciente['nombre3'],
            ':pac_apellido1' => $paciente['apellido1'],
            ':pac_apellido2' => $paciente['apellido2'],
            ':pac_apellidoc' => $paciente['apellidoC'],
            ':pac_foto' => $paciente['foto'],
            ':pac_direccion' => $paciente['direccion'],
            ':pac_telefono' => $paciente['telefono'],
            ':pac_correo' => $paciente['correo'],
            ':pac_telefono_emg' => $paciente['telEmergencia'],
            ':pac_fec_nac' => $paciente['fecNac'],
            ':pac_ocupacion' => $paciente['ocupacion'],
            ':nac_id' => $paciente['nacionalidadId'],
            ':ec_id' => $paciente['estadoCivil'],
        );

        // Ejecuta la consulta sin commit
        $resul = _queryBindNoCommit($conexion, $params, $strInsPaciente);

        if (!$resul) {
            throw new Exception("Error inesperado al intentar guardar la información personal del paciente.");
        }

        //INSERCIÓN A EMR_DATOS_CLINICOS

        //Se obtienen y comvierten los pesos 
        $pesoLB = number_format((float)$datClinico['pesoLB'], 2, '.', '');
        $pesoKG = number_format($pesoLB / 2.205, 2, '.', '');

        $strInsDatosClinicos = 
            "INSERT INTO EMR.EMR_DATOS_CLINICOS
            (
            PAC_CORR,
            PAC_DOCNUM,
            DC_GENERO,
            DC_PESOKG_ACTUAL,
            DC_PESOLB_ACTUAL,
            DC_ALTURACM_ACTUAL,
            DC_ULT_MED_PA_MMHG,
            DC_ULT_MED_FC_LPM,
            ID_TS
            )
            VALUES
            (
            :pac_corr,
            :pac_docnum,
            :dc_genero,
            $pesoKG,--No se bindea por temas de incompatibilidad con tipos float.
            $pesoLB,--No se bindea por temas de incompatibilidad con tipos float.
            :dc_alturacm_actual,
            :dc_ult_med_pa_mmhg,
            :dc_ult_med_fc_lpm,
            :id_ts
            )";

        // Parámetros de la consulta
        $params = array(
            ':pac_corr' => $paciente['correlativo'],
            ':pac_docnum' => $paciente['docId'],
            ':dc_genero' => $datClinico['genero'],
            ':dc_alturacm_actual' => $datClinico['altura'],
            ':dc_ult_med_pa_mmhg' => $datClinico['presionArt'],
            ':dc_ult_med_fc_lpm' => $datClinico['freqCardiaca'],
            ':id_ts' => $datClinico['tipoSangre'],
        );

        // Ejecuta la consulta sin commit
        $resul = _queryBindNoCommit($conexion, $params, $strInsDatosClinicos);

        if (!$resul) {
            throw new Exception("Error inesperado al intentar guardar la información biométrica y vital del paciente.");
        }

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
        'desc' => 'Paciente ingresado correctamente.',
    );

}