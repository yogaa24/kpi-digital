<!DOCTYPE html>
<?php
require 'config.php';
session_start();
// echo $_SESSION['username'];
$username = $_SESSION['username'];
$no = 1;
$nama1;
$nik;
$departement;
$jabatan;
$sql = "SELECT * FROM users WHERE username='$username'";
$result = mysqli_query($conn, $sql);
while ($hasil = mysqli_fetch_assoc($result)) {
    //echo $hasil['password'];
    $nama1 = $hasil['username'];
    $nik = $hasil['nik'];
    $departement = $hasil['departement'];
    $jabatan = $hasil['jabatan'];
}
$what1descripsi;
$what1hasil;
$what1bobot;
$what1nilai;
$what1total;
$what1id;
$sql2 = "SELECT * FROM what1 WHERE username='$username'";
$result2 = mysqli_query($conn, $sql2);

?>

<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8" />
    <!--START DRIVER-->
    <!--BOOTSTRAP-->
    <link href="css/bootstrap.min.css" rel="stylesheet" />
    <link href="css/oke.css" rel="stylesheet" />
    <link rel="icon" href="img/icon.png" type="image/icon type">
    <script src="js/bootstrap.bundle.min.js"></script>
    <!--FONT AWESOME-->
    <link rel="stylesheet" href="fontawesome/css/font-awesome.min.css" />
    <!--DATA TABLES-->
    <link rel="stylesheet" type="text/css" href="datatables/datatables.min.css" />
    <script type="text/javascript" src="datatables/datatables.min.js"></script>

    <!--END DRIVER-->
    <title>REQUEST DESIGN</title>
    <title>Biodata</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.12.1/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Roboto", sans-serif;
        }

        header {
            position: fixed;
            background: #22242A;
            padding: 20px;
            width: 100%;
            height: 30px;
        }

        img {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        tbody {
            font-size: 17px;
            font-weight: 300;
            color: #e8e8e8;
        }

        .tabelku {
            width: 800px;
            margin: auto;
            text-align: center;
        }

        .tabelb {
            width: auto;
            margin: auto;
        }

        .left_area span {
            color: #9f2ebb;
        }

        .left_area h3 {
            color: #fff;
            margin: 0;
            text-transform: uppercase;
            font-size: 22px;
            font-weight: 900;
        }

        .right_area img {
            padding: 5px;
            float: right;
            margin-top: -47px;
            margin-right: 40px;
        }

        .sidebar {
            background: #2f323a;
            margin-top: 70px;
            padding-top: 30px;
            position: fixed;
            left: 0;
            width: 250px;
            height: 100%;
        }

        .sidebar .profile_image {
            width: 100px;
            height: 100px;
            border-radius: 100px;
            margin-bottom: 10px;
        }

        .sidebar h4 {
            color: #ccc;
            margin-top: 0;
            margin-bottom: 20px;
        }

        .sidebar a {
            color: #fff;
            display: block;
            width: 100%;
            line-height: 60px;
            text-decoration: none;
            padding-left: 40px;
            box-sizing: border-box;
            transition: 0.5s;
            transition-property: background;
        }

        .sidebar a:hover {
            background: #9f2ebb;
        }

        .sidebar i {
            padding-right: 10px;
        }

        .content {
            margin-left: 10px;
            padding-top: 30px;
            background: url(bcg.jpg);
            background-position: center;
            background-size: cover;
            height: 125vh;
        }

        .box {
            width: 740px;
            margin: 0 auto;
            margin-top: 30px;
            box-shadow: 0 0.30rem 0.75rem rgba(5, 5, 5, 5);
            transition: all .3s;
            background-color: #591869;
            border: solid 5px #ea92ff;
            border-top-right-radius: 80px;
            border-bottom-left-radius: 80px;
        }

        .box:hover {
            background-color: #1f8a45;
            border: solid 5px #4fd47e;
            border-top-left-radius: 80px;
            border-bottom-right-radius: 80px;
            border-top-right-radius: 0px;
            border-bottom-left-radius: 0px;
        }

        .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100%;
            background-color: #22242A;
            color: #e8e8e8;
            text-align: center;
        }
    </style>
</head>

<body>


    <header>
        <div class="left_area">
            <h3>KARISMA INDOAGRO UNIVERSAL </h3>
        </div>
        <div class="right_area">

        </div>
    </header>
    <div class="sidebar">
        <center>
            <img src="asyrof1.jpg" class="profile_image" alt="logo / FOTO">
            <h4>NAMA KARYAWAN - <?php echo $nama1; ?></h4>
        </center>
        <a href="#"><i class="fas fa-desktop"></i><span>Dashboard KPI</span></a>
        <a href="#"><i class="fas fa-table"></i><span>Riwayat Pendidikan</span></a>
        <a href="#"><i class="fas fa-list-ul"></i><span>Daftar Favorit</span></a>
        <a href="formdaftar.html"><i class="fas fa-pencil-alt"></i><span>Formulirku</span></a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Keluar</span></a>
    </div>

    <div class="content">
        <div class="box">
            <tbody>
                <table border="0" width="100%"
                    style="padding-left: 55px; padding-right: 0px; padding-top: 15px; padding-bottom: 15px;">
                    <tr>
                        <th rowspan="8">
                            <img src="fotoku.jpg" alt="fotoku" width="110px" height="160px">
                        </th>
                    </tr>
                    <tr>
                        <td width="28%">Nama</td>
                        <td width="2%">:</td>
                        <td style="font-weight: bold"><?php echo $nama1; ?></td>
                    </tr>
                    <tr>
                        <td>NIK</td>
                        <td>:</td>
                        <td><?php echo $nik; ?></td>
                    </tr>
                    <tr>
                        <td>Departement</td>
                        <td>:</td>
                        <td><?php echo $departement; ?></td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>:</td>
                        <td><?php echo $jabatan; ?></td>
                    </tr>
                </table>
            </tbody>
        </div>
        <br />
        <table class="tabelku" border="1" cellspacing="1" cellpadding="5">
            <tr>
                <th style="background-color: #2f323a;" rowspan="2">NO</th>
                <th style="background-color: #2f323a;" colspan="8">Detail What</th>
            </tr>
            <tr>
                <th style="background-color: #2f323a;">id</th>
                <th style="background-color: #2f323a;">DESCRIPSI</th>
                <th style="background-color: #2f323a;">HASIL</th>
                <th style="background-color: #2f323a;">BOBOT</th>
                <th style="background-color: #2f323a;">NILAI</th>
                <th style="background-color: #2f323a;">TOTAL NILAI</th>
                <th style="background-color: #2f323a;">ACTION</th>
            </tr>
            <?php while ($hasil2 = mysqli_fetch_assoc($result2)) {
                //echo $hasil['password'];
                $what1descripsi = $hasil2['descripsi'];
                $what1hasil = $hasil2['hasil'];
                $what1bobot = $hasil2['bobot'];
                $what1nilai = $hasil2['nilai'];
                $what1total = $hasil2['bobot'] * $hasil2['nilai'] / 100;
                $what1id = $hasil2['id'];
            ?>

                <tr>
                    <td style="background-color: #2f323a;">
                        <center>
                            <?php echo $no++; ?>.
                        </center>
                    </td>
                    <td style="background-color: #2f323a;"><?php echo $what1id; ?></td>
                    <td style="background-color: #2f323a;"><?php echo $what1descripsi; ?></td>
                    <td style="background-color: #2f323a;"><?php echo $what1hasil; ?></td>
                    <td style="background-color: #2f323a;"><?php echo $what1bobot; ?></td>
                    <td style="background-color: #2f323a;"><?php echo $what1nilai; ?></td>
                    <td style="background-color: #2f323a;"><?php echo $what1total; ?></td>
                    <td style="background-color: #2f323a;">
                        <!--Ubah-->
                        <a href="kelola.php?ubah=<?php echo $hasil2['id']; ?> " type="button" class="btn btn-warning btn-sm">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <!--Hapus-->
                        <a href="#" type="button" class="btn btn-danger btn-sm" onClick="return confirm('Apakah Anda Yakin Ingin Menghapus Data Tersebut ???')">
                            <i class="fa fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php
            }
            ?>
        </table>
        <br />
        <table class="tabelb" border="1" cellspacing="1" cellpadding="5">
            <th style="background-color: #22242A; text-align: center" colspan="2">Daftar Favorit</th>
            <tr>
                <th style="background-color: #591869; padding-right: 30px; text-align: left">
                    <ul>
                        <li> Makanan Favorit :
                            <ol>
                                <li>Bakso</li>
                                <li>Mie Ayam</li>
                                <li>Soto Ayam</li>
                                <li>Nasi Padang</li>
                                <li>Rawon</li>
                            </ol>
                        </li>
                    </ul>
                <th style="background-color: #591869; padding-right: 30px; text-align: left">
                    <ul>
                        <li> Minuman Favorit :
                            <ol>
                                <li>Air Mineral</li>
                                <li>Es Teh Anget</li>
                                <li>Susu</li>
                                <li>Jus Alpukat</li>
                                <li>Degan</li>
                            </ol>
                        </li>
                    </ul>
                </th>
            </tr>
        </table>
    </div>
    <div class="footer">
        <h3>Copyright © 2021 Moch. Shohibul Asyrof</h3>
    </div>
</body>

</html>