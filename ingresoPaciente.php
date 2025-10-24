<?php 
session_start();
require_once "include/sidebar/sidebar.php";
require_once "core_functions/core_functions.php";

if (validarPermiso(1)){
?>

<div class="content-container">
    <?php
    include 'module/ingreso_paciente/view/Frm_Paciente.php';
    ?>
</div>

<?php 

} else {

    include 'include/unauthorized.php';
}

?>