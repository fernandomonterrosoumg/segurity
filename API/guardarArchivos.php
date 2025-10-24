<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Private-Network: true');
error_reporting(E_ERROR | E_PARSE);

switch ($_POST['ROOT']) {
    case 'foto_paciente':
        $RUTA = 'C:/xampp/htdocs/EMRApp/adjunto/FotosPacientes/';
        break;
    case 'receta_medica':
        $RUTA = 'C:/xampp/htdocs/EMRApp/adjunto/ArchivosRecetas/';
        break;
}

try {
    $conteo = count($_FILES['ARCHIVO']['name']);

    for ($i = 0; $i < $conteo; $i++) {
        $ubicacionTemporal = $_FILES["ARCHIVO"]["tmp_name"][$i];
        $NOMBRE = $_FILES["ARCHIVO"]["name"][$i];
        $extension = pathinfo($NOMBRE, PATHINFO_EXTENSION);
        // Renombrar archivo
        $nameFile = $_POST['fileName'][$i];
        $nuevoNombre = $nameFile.".".$extension;
        $ubicacion = $RUTA.$nuevoNombre;

        if(move_uploaded_file($ubicacionTemporal,$ubicacion)){
            $message = array(
            "archivo" => $nuevoNombre,
            "message" => "Archivo guardado correctamente."
            );
            http_response_code(200);
        }else{
            $message = array(
            "archivo" => "error",
            "message" => "Error al cargar el archivo: ".$NOMBRE
            );
            http_response_code(500);
        };
        $response[] = $message;
    }
} catch (Exception $e) {
    // Manejar la excepción aquí
    $error = array(
        "error" => "Error al guardar archivo: " . $e->getMessage()
    );
    http_response_code(500);
    echo json_encode($error);
}
echo json_encode($response);
?>