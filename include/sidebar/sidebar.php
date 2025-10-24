<?php
if (!(isset($_SESSION['usuario_logeado']) and $_SESSION['usuario_logeado'])){
    include "login/login.php";
    exit;
    }
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMRApp</title>
    <link rel="stylesheet" href="/EMRApp/include/sidebar/css/sidebar.css">
    <link rel="icon" type="image/webp" href="/EMRApp/include/Images/esculapio1.webp">
    <link rel="stylesheet" href="/EMRApp/include/css_lib/general.css">
</head>
<body>
    <div id="app1">
        <div class="sidebar-wrapper">
            <div class="sidebar" id="sidebar">
                <ul id="modulosList">
                    <div class="profile">
                        <img style="border-radius: 50%; border: 4px solid #d3d3d3; object-fit: cover;" src="/EMRApp/include/Images/user.png" alt="profile pic">
                        <span style="color: #F5F5F5; font-size: 24px; font-family: Arial, sans-serif;">
                            <?php echo $_SESSION['nombre1']. ' ' . $_SESSION['apellido1'] ?>
                        </span>
                    </div>

                    <div class="indicator" id="indicator"></div>
                    
                    <!-- Aquí se insertarán los módulos dinámicamente -->
                </ul>
            </div>
            <button class="toggle-btn" id="toggleBtn">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
    <script src="/EMRApp/include/js_lib/sweetAlert2/11.12.3.js"></script>
    <script src="/EMRApp/include/js_lib/iconify/2.1.0.js"></script>
    <script src="/EMRApp/include/sidebar/js/sidebar.js"></script>
</body>
</html>
