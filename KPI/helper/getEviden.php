<?php
require 'config.php';

$iduser = $_SESSION['id_user'];

$sqlevi= "SELECT * FROM tb_eviden where id_user = $iduser";
$resulteviden = mysqli_query($conn, $sqlevi);

?>  