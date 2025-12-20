<?php
session_start();
require 'helper/config.php';

if (isset($_SESSION['id_user'])) {
    header("Location: dashboard");
    exit();
}

if (isset($_POST['submit'])) {
    $username = $_POST['username'];
    $namalengkap = $_POST['namalengkap'];
    $nik = $_POST['nik'];
    $departemen = $_POST['departemen'];
    $jabatan = $_POST['jabatan'];
    $bagian = $_POST['bagian'];
    $atasan = $_POST['atasan'];
    $penilai = $_POST['penilai'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $code = $_POST['codee'];
    
    if($code == '666'){
        if ($password == $cpassword) {
            $sqls = "SELECT * FROM tb_users WHERE nama_lngkp='$namalengkap'";
            $results = mysqli_query($conn, $sqls);
            if (!$results->num_rows > 0) {
                $sql = "INSERT INTO tb_users (`username`, `nama_lngkp`, `nik`, `bagian`, `departement`, `jabatan`, `atasan`,`penilai`)
                        VALUES ('$username','$namalengkap','$nik','$bagian','$departemen','$jabatan','$atasan','$penilai')";
                $result = mysqli_query($conn, $sql);
                if ($result) {
                    $sqlasf = "Select id from tb_users where username='" . $username . "';";
                    $resultsg = mysqli_query($conn, $sqlasf);
                    $okkosa = 0;
                    while ($okko = mysqli_fetch_assoc($resultsg)) {
                        $okkosa = $okko['id'];
                    }
                    $isiss=1;
                    if($jabatan=='Kabag'){
                        $isiss = 2;
                    }else if($jabatan=='Karyawan'){
                        $isiss = 1;
                    }else if($jabatan=='Kadep'){
                        $isiss = 3;
                    }
                    $sqlss = "INSERT INTO tb_auth (`id_user`, `password`, `level`)
                        VALUES ($okkosa,'$password',1)";
                    $resultss = mysqli_query($conn, $sqlss);

                    $sqasl = "INSERT INTO tb_bobotkpi (`id_user`, `bobotwhat`, `bobothow`)
                        VALUES ($okkosa,0,0)";
                    $resultsd = mysqli_query($conn, $sqasl);

                    if ($resultss && $resultsd) {
                        header("Location: index");
                    } else {
                        echo "<script>alert('" . $resultss . $resultsd . "')</script>";
                    }
                } else {
                    echo "<script>alert('Woops! Terjadi kesalahan.')</script>";
                }
            } else {
                echo "<script>alert('Woops! Nama Sudah Terdaftar.')</script>";
            }
        } else {
            echo "<script>alert('Password Tidak Sesuai')</script>";
        }
    }else{
        echo "<script>alert('Kode Salah')</script>";
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/style.css">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <title>KPI Digital | Register</title>

    <style>
        .hidden {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container" style="width: 600px">
        <form action="" method="POST" class="login-email">
            <p class="login-text" style="font-size: 2rem; font-weight: 800; margin-top: -15px; margin-bottom: 15px;">
                Register</p>
            <div id="form1">
                <div class="input-group" style="margin-bottom: 10px;">
                    <input type="text" placeholder="Username" name="username" required>
                </div>
                <div class="input-group" style="margin-bottom: 10px;">
                    <input type="password" placeholder="Password" name="password" required>
                </div>
                <div class="input-group" style="margin-bottom: 10px;">
                    <input type="password" placeholder="Ulangi Password" name="cpassword" required>
                </div>
                <div class="input-group" style="margin-bottom: 10px;">
                    <input type="text" placeholder="Nama Lengkap" name="namalengkap" required>
                </div>
            </div>
            <div id="form3" class="hidden">
                <form method="POST" action="#">
                    <div class="form-group ml-2 mr-2 mb-2">
                        <input type="text" class="form-control" placeholder="Nomor Induk Karyawan" name="nik" required>
                    </div>

                    <div class="form-group ml-2 mr-2 mb-2">
                        <select class="custom-select" id="departemen" name="departemen" required>
                            <option value="" disabled selected>Pilih Departemen</option>
                            <option value="Keuangan & HRD">Keuangan & HRD</option>
                            <option value="Sales & Marketing">Sales & Marketing</option>
                            <option value="Logistik">Logistik</option>
                            <option value="GA">GA</option>
                        </select>
                    </div>

                    <div class="form-group ml-2 mr-2 mb-2">
                        <input type="text" class="form-control" placeholder="Bagian" name="bagian" required>
                    </div>

                    <div class="form-group ml-2 mr-2 mb-2">
                        <select class="custom-select" name="jabatan" id="jabataninn" required>
                            <option value="" disabled selected>Pilih Jabatan</option>
                            <option value="Kabag">Kabag</option>
                            <option value="Kadep">Kadep</option>
                            <option value="Karyawan">Karyawan</option>
                        </select>
                    </div>

                    <div class="form-group ml-2 mr-2 mb-2">
                        <select class="custom-select" id="pickone" name="atasan" required>
                            <option value="">Atasan Langsung</option>
                        </select>
                    </div>

                    <div class="form-group ml-2 mr-2 mb-2">
                        <input type="text" class="form-control" id="penilai" placeholder="Penilai" name="penilai" required>
                    </div>
                    <div class="form-group ml-2 mr-2 mb-2">
                        <input type="password" class="form-control" placeholder="Kode (Minta IT)" name="codee" required>
                    </div>
                    <div class="input-group ml-2 mr-2 mt-3">
                        <button type="submit" name="submit" class="btn btn-primary">Register</button>
                    </div>
                </form>
            </div>
            <script>
                document.getElementById('departemen').addEventListener('change', function() {
                    const penilaiInput = document.getElementById('penilai');
                    var items ;
                    
                    if (this.value === 'Keuangan & HRD') {
                        penilaiInput.value = 'Diana Wulandari';
                        items = ["Pilih Kabag", "Vita Ari Puspita", "Arini Dina Yasmin", "Ahmad Syaiti", "Wahyu Arif Prasetyo", "Riza Dwi Fitrianingtyas"];
                    } else if (this.value === 'Sales & Marketing') {
                        penilaiInput.value = 'Heru Sucahyo';
                        items = ["Evi Yulia", "Heru Sucahyo"];
                    }else if (this.value === 'Logistik') {
                        penilaiInput.value = 'Kurniawan';
                        items = ["Kurniawan", "Alif"];
                    }else if (this.value === 'GA') {
                        penilaiInput.value = 'Nandang Ernoko';
                        items = ["Nandang", "Wawan"];
                    }

                    var str = ""
                    for (var item of items) {
                        str += "<option>" + item + "</option>"
                    }
                    document.getElementById("pickone").innerHTML = str;
                });

                document.getElementById('jabataninn').addEventListener('change',function(){
                    const deptInput = document.getElementById('departemen');
                    var items ;

                    if(this.value=='Kabag'){
                        items = ["Pilih Kabag", "Diana Wulandari"];
                    }

                    var str = ""
                    for (var item of items) {
                        str += "<option>" + item + "</option>"
                    }
                    document.getElementById("pickone").innerHTML = str;
                })

            </script>
            <!-- <p class="login-register-text">Anda sudah punya akun? <a href="index.php">Login</a></p> -->
        </form>
        <div id="form2">
            <div class="input-group" style="margin-top: 25px; margin-bottom: 10px;">
                <button name="next" class="btn" id="next">Lengkapi Data</button>
            </div>
        </div>
    </div>
</body>
<script>
    setHiddable("next", "form1");
    setHiddable("next", "form2");
    setHiddable("next", "form3");
    function setHiddable(btnId, elementId) {
        const btn = document.querySelector(`#${btnId}`);
        btn.addEventListener("click", (ev) => {
            const targetEl = document.querySelector(`#${elementId}`);
            targetEl.classList.toggle("hidden");
        });
    }
</script>


</html>