<?php
session_start();

$_SESSION['usuario_logeado'] = false;

// Destroy entire session data
session_destroy();

// Redirect page to index.php
header('location:index.php');
exit();

?>
