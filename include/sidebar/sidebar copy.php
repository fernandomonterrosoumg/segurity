<meta charset="UTF-8">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>EMRApp</title>
<link rel="stylesheet" href="/EMRApp/include/sidebar/css/sidebar.css">
<body>
    <div id="app1">
        <div class="sidebar-wrapper">
            <div class="sidebar" id="sidebar">
                <ul>
                    <div class="profile">
                        <img style="border-radius: 50%; border: 4px solid #d3d3d3; object-fit: cover;" src="/EMRApp/include/Images/user.png" alt="profile pic">
                        <span><?php echo $_SESSION['nombre1']. ' ' . $_SESSION['apellido1']?></span>
                    </div>
        
                    <div class="indicator" id="indicator"></div>
                                       
                    
                    <li v-for="(modulo, index) in modulos" :key="index" @mouseover="moverIndicador($event)">
                        <a class="achor-sidebar" :href="modulo.MDL_RUTA">
                            <iconify-icon :icon="modulo.MDL_IMG" width="1.8em" height="1.8em" style="color: white;"></iconify-icon>
                            <span>{{modulo.MDL_NOM}}</span>
                        </a>
                    </li>
                    <li @mouseover="moverIndicador($event)">
                        <a href="#" style="display: flex;">
                            <iconify-icon icon="material-symbols:logout" width="1.8em" height="1.8em"  style="color: white"></iconify-icon>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>
                    <li @mouseover="moverIndicador($event)">
                        <a href="#" style="display: flex;">
                            <iconify-icon icon="material-symbols:logout" width="1.8em" height="1.8em"  style="color: white"></iconify-icon>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>
                    <li @mouseover="moverIndicador($event)">
                        <a href="#" style="display: flex;">
                            <iconify-icon icon="material-symbols:logout" width="1.8em" height="1.8em"  style="color: white"></iconify-icon>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>
                    <li @mouseover="moverIndicador($event)">
                        <a href="#" style="display: flex;">
                            <iconify-icon icon="material-symbols:logout" width="1.8em" height="1.8em"  style="color: white"></iconify-icon>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>
                    <li @mouseover="moverIndicador($event)">
                        <a href="#" style="display: flex;">
                            <iconify-icon icon="material-symbols:logout" width="1.8em" height="1.8em"  style="color: white"></iconify-icon>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>
                    <li @mouseover="moverIndicador($event)">
                        <a href="#" style="display: flex;">
                            <iconify-icon icon="material-symbols:logout" width="1.8em" height="1.8em"  style="color: white"></iconify-icon>
                            <span>Cerrar Sesión</span>
                        </a>
                    </li>


                    
                </ul>
            </div>
            <button class="toggle-btn" id="toggleBtn" @click="deplegarSidebar">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>
    
    <script src="/EMRApp/include/js_lib/VUEjs/3.4.33.js"></script>
    <script src="/EMRApp/include/sidebar/js/sidebar.js"></script>
    <script src="/EMRApp/include/js_lib/iconify/2.1.0.js"></script>
    <script src="/EMRApp/include/js_lib/sweetAlert2/11.12.3.js"></script>
    