<?php 
session_start();
require_once "include/sidebar/sidebar.php";
require_once "core_functions/core_functions.php";

if (validarPermiso(3)){
?>

<div class="content-container">
    <?php
    include 'module/ingreso_cita/view/Frm_Cita.php';
    ?>
</div>

<?php 

} else {

    include 'include/unauthorized.php';
}

?>