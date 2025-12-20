<?php
require 'config.php';;

$sql3 = "SELECT sum(nilai) FROM tb_whats WHERE id_user=1";
$result3 = mysqli_query($conn, $sql3);

$totalnilai;
while($row3 = mysqli_fetch_array($result3)){
    $totalnilai=$row3['sum(nilai)'];  

    echo "$totalnilai";
}

?>
