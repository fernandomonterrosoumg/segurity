<?php
//$oracle = oci_connect("EMR_ACCESSUSER", "4CC3SS4S3R", "localhost/EMRDB");

//Funciones para OCI8
/**
 * Establece una conexión con la base de datos Oracle.
 *
 * Esta función intenta conectar con una base de datos Oracle usando los parámetros 
 * especificados para el usuario, contraseña, y los detalles de conexión.
 * 
 * - Si la conexión es exitosa, devuelve el identificador de conexión a la base de datos.
 * - Si la conexión falla, se genera un error y se devuelve `false`.
 *
 * @return resource|false Devuelve el identificador de conexión (`resource`) si la conexión es exitosa, 
 *                         o `false` si ocurre un error en la conexión.
 */
function _connectDB() {
    $usuario = "EMR_ACCESSUSER";
    $contraseña = "ACC3SS4S3R";
    // Usa SERVICE_NAME (FREEPDB1), no SID:
    $remoto = "localhost:1521/FREEPDB1"; // o "127.0.0.1:1521/FREEPDB1"

    $conexion = oci_connect($usuario, $contraseña, $remoto, 'AL32UTF8');
    if (!$conexion) { $e = oci_error(); trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR); }
    return $conexion;
}


/**
 * Realiza una consulta a la base de datos y devuelve los resultados.
 * 
 * @param string $strConsulta La consulta SQL a ejecutar.
 * @return array Los resultados de la consulta.
 */
function _query($strConsulta = "") {
    try 
    {
        // Asegurarse de que la consulta no esté vacía
        if (empty($strConsulta)) {
            throw new Exception("La consulta SQL no puede estar vacía.");
        }

        // Establecer la conexión
        $conexion = _connectDB();
        if (!$conexion) {
            throw new Exception("Error de conexión a la base de datos.");
        }

        // Preparar y ejecutar la consulta
        $consulta = oci_parse($conexion, $strConsulta);
        if (!$consulta) {
            $e = oci_error($conexion);
            throw new Exception("Error al preparar la consulta: " . htmlentities($e['message'], ENT_QUOTES));
        }

        $ejecucion = oci_execute($consulta);
        if (!$ejecucion) {
            $e = oci_error($consulta);
            throw new Exception("Error al ejecutar la consulta: " . htmlentities($e['message'], ENT_QUOTES));
        }

        // Obtener los resultados
        $arrData = [];
        while ($arrTMP = oci_fetch_array($consulta, OCI_ASSOC+OCI_RETURN_NULLS)) {
            $arrData[] = $arrTMP;
        }
        
    } 
    catch (Exception $e) 
    {
        trigger_error($e->getMessage(), E_USER_ERROR);
    }
    finally
    {
        oci_free_statement($consulta);
        oci_close($conexion);
    }

    return $arrData;
}

/**
 * Realiza una operación DML a la base de datos bindeando las variables y devuelve los resultados.
 * 
 * @param string $strConsulta La consulta SQL a ejecutar.
 * @param array $params El arreglo de clave - valor para bindear.
 * @return array Los resultados de la consulta.
 */
function _queryBind($strConsulta, $params)
{
    try
    {
        $conexion = _connectDB();
        $stid = oci_parse($conexion, $strConsulta);

        if (!$stid) {
            $e = oci_error($conexion);
            throw new Exception(htmlentities($e['message'], ENT_QUOTES));
        }

        if (!is_array($params)) {
            $params = array();
        } 

        foreach ($params as $paramName => &$paramValue) {
            if (is_numeric($paramValue)) {
                oci_bind_by_name($stid, $paramName, $paramValue, -1, SQLT_INT);
            } else {
                oci_bind_by_name($stid, $paramName, $paramValue);
            }
        }

        $result = oci_execute($stid);

        if (!$result) {
            $e = oci_error($stid);
            throw new Exception(htmlentities($e['message'], ENT_QUOTES));
        }

        $arrData = array();
        while ($row = oci_fetch_assoc($stid)) {
            $arrData[] = $row;
        }
    }
    catch (Exception $e)
    {
        throw $e;
    }
    finally
    {
        oci_free_statement($stid);
        oci_close($conexion);
    }

    return $arrData;
}

/**
 * Ejecuta querys que se confirman automaticamente.
 * 
 * @param string $strQuery La consulta SQL a ejecutar.
 * @param array $params El arreglo de clave - valor para bindear.
 * @return boolean $resul TRUE si se ejecuta correctamente o FALSE si falla la ejecucion.
 */
function _queryBindCommit($params, $strQuery = "")
{
    try {

        $conexion = _connectDB();

        $stid = oci_parse($conexion, $strQuery);

        if (!$stid) {
            $e = oci_error($conexion);
            throw new Exception(htmlentities($e['message'], ENT_QUOTES));
        }

        if (!is_array($params)) {
            $params = array();
        } 

        foreach ($params as $paramName => &$paramValue) {
            oci_bind_by_name($stid, $paramName, $paramValue);            
        }

        $resul = oci_execute($stid);

        if (!$resul) {
            $e = oci_error($stid);
            throw new Exception(htmlentities($e['message'], ENT_QUOTES));
        }
    } catch (Exception $e) {
        throw $e;
    }

    return $resul;
}

/**
 * Ejecuta querys que no se confirmarán, utilizado para realizar transacciones.
 * 
 * @param string $strQuery La consulta SQL a ejecutar.
 * @param array $params El arreglo de clave - valor para bindear.
 * @param resource $conexion La conexión a la base de datos que no va a hacer commit.
 * @return boolean $resul TRUE si se ejecuta correctamente o FALSE si falla la ejecucion.
 */
function _queryBindNoCommit($conexion, $params, $strQuery = "")
{
    try {
        $stid = oci_parse($conexion, $strQuery);

        if (!$stid) {
            $e = oci_error($conexion);
            throw new Exception(htmlentities($e['message'], ENT_QUOTES));
        }

        if (!is_array($params)) {
            $params = array();
        } 

        foreach ($params as $paramName => &$paramValue) {
            oci_bind_by_name($stid, $paramName, $paramValue);            
        }

        $resul = oci_execute($stid, OCI_NO_AUTO_COMMIT);

        if (!$resul) {
            $e = oci_error($stid);
            throw new Exception(htmlentities($e['message'], ENT_QUOTES));
        }
    } catch (Exception $e) {
        throw $e;
    }

    return $resul;
}

/**
 * Realiza un rollback de la transaccion y cierra la conexión.
 * 
 * @param resource $conexion La conexion a la que se le dara rollback.
 */
function _queryRollback($conexion){
    oci_rollback($conexion);
    oci_close($conexion);
}

/**
 * Realiza un commit de la transaccion y cierra la conexión.
 * 
 * @param resource $conexion La conexion a la que se le dara commit.
 */
function _queryCommit($conexion){
    oci_commit($conexion);	
    oci_close($conexion);
}

function validarPermiso($mdl_id) {

    $rol_id = $_SESSION['rol_id'];

    $strConsulta = "SELECT COUNT(*) CANTIDAD FROM EMR.EMR_ACCE_MODULOROL WHERE ROL_ID = $rol_id AND MDL_ID = $mdl_id";

    $arrConsulta = _query($strConsulta);

    if ($arrConsulta[0]["CANTIDAD"] >= 1) {
        return true;
    } else {
        return false;
    }
}


function _findOrCreateUserFromGoogle($email, $name, $googleSub) {
  // 1) Buscar usuario por email
  $sql = "SELECT id, email FROM EMR_USUARIOS WHERE email = :email";
  $row = _queryOne($sql, [':email' => $email]); // asume que ya tienes helpers _queryOne/_execute

  if ($row) {
    // Opcional: actualizar nombre o google_sub si no lo tenías
    _execute("UPDATE EMR_USUARIOS SET google_sub = :sub WHERE id = :id AND (google_sub IS NULL OR google_sub <> :sub)",
             [':sub'=>$googleSub, ':id'=>$row['id']]);
    return $row;
  }

  // 2) Crear usuario nuevo (ajusta columnas a tu esquema real)
  _execute(
    "INSERT INTO EMR_USUARIOS (email, nombre_completo, google_sub, creado_en)
     VALUES (:email, :nombre, :sub, GETDATE())",
    [':email'=>$email, ':nombre'=>$name, ':sub'=>$googleSub]
  );

  $new = _queryOne("SELECT id, email FROM EMR_USUARIOS WHERE email = :email", [':email'=>$email]);
  return $new;
}

?>
