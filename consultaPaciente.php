<?php 
session_start();
require_once "include/sidebar/sidebar.php";
require_once "core_functions/core_functions.php";

if (validarPermiso(2)){
?>

<div class="content-container">
    <?php
    include 'module/consulta_pacientes/view/Tbl_Paciente.php';
    ?>
</div>

<?php 

} else {

    include 'include/unauthorized.php';
}

?>