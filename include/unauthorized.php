<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso no autorizado</title>
    <!-- Incluimos Bootstrap 5 -->
    <link href="/EMRApp/include/css_lib/bootstrap/5.0.2.css" rel="stylesheet">
</head>
<body>
    <style>
        /* Estilos personalizados para la imagen */
        img {
            max-width: 100%;
            height: auto;
            margin-bottom: 20px;
        }

        /* Estilo personalizado para el botón */
        .bottom-right {
            position: absolute;
            bottom: 20px;
            right: 20px;
        }
    </style>

    <div class="container d-flex justify-content-center align-items-center vh-100">
        <div class="position-relative bg-white p-4 rounded shadow" style="max-width: 500px;">
            <h1 class="text-center">No tiene los permisos suficientes para acceder a este módulo</h1>
            <img src="/EMRApp/include/Images/unauthorized.webp" alt="Acceso no autorizado" class="mx-auto d-block">
            <a href="/EMRApp/index.php" class="btn btn-primary bottom-right">Volver al inicio</a>
        </div>
    </div>

</body>
</html>
