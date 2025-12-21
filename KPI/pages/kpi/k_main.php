<div class="row">
    <div class="col-lg connectedSortable">
        <div class="d-flex">
            <div class="card mb-4 w-50" style="margin-right:7px;">
                <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
                    <h5 style="color:white;" class="card-title"><?= $poin; ?></h5>
                    <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">Bobot :
                        <?= $bobot ?>%
                    </h5>
                    <div class="card-tools">
                        <button style="color: white; margin-top: -20px; margin-right: 5px;" type="button"
                            data-bs-toggle="modal" data-bs-target="#EditModal<?= $idKPI ?>" class="btn btn-tool">
                            <i class="bi bi-pencil fs-6"></i>
                        </button>

                        <button style="color: white; margin-top: -20px; margin-right: 5px; " type="button"
                            data-bs-toggle="dropdown" class="btn btn-tool dropdown-toggle">
                            <i class="bi bi-plus-circle fs-6"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#WhatModal<?= $idKPI ?>">Tambah What </a>
                        </div>
                        <button style="color: white;" type="button" class="btn btn-tool"
                            data-lte-toggle="card-collapse">
                            <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                            <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Whats</th>
                                <th style="width: 30%">Hasil</th>
                                <th style="width: 5%">
                                    <center>Nilai</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Bobot</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Total</center>
                                </th>
                                </th>
                                <th style="width: 9%">
                                    <center>Action</center>
                                </th>
                            </tr>
                        </thead>
                        <?php $sql1 = "SELECT * FROM tb_whats WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                        $ql = mysqli_query($conn, $sql1);
                        while ($res = mysqli_fetch_assoc($ql)) {
                            ?>
                            <tbody>
                                <tr class="align-middle">
                                    <td><?= $res['p_what']; ?></td>
                                    <td><?= $res['hasil']; ?></td>
                                    <td>
                                        <center><?= $res['nilai']; ?>
                                    </td>
                                    <td>
                                        <center><?= $res['bobot']; ?>%
                                    </td>
                                    <td>
                                        <center><?= $res['total']; ?>
                                    </td>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                            <i class="bi bi-eye fs-8"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                                            <a class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#HapusWhatModal<?= $res['id_what'] ?>">Hapus</a>
                                            <div class="dropdown-divider"></div>
                                            <a class="dropdown-item fw-bolder" data-bs-toggle="modal"
                                                data-bs-target="#NilaiWhatModal<?= $res['id_what'] ?>">Nilai</a>
                                        </div>
                                    </td>

                                    <?php include('pages/kpi/k_modalHapuswhat.php'); ?>

                                </tr>
                            </tbody>
                            <div class="modal fade" id="NilaiWhatModal<?=$res['id_what'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"> 
                                        <div class="modal-header"> 
                                            <h5 class="modal-title fw-bold" id="exampleModalLabel">Penilaian</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <input type="hidden" value="<?php echo $res['id_what']; ?>" name="idkpi"> 
                                                
                                                <div class="input-group mb-3">
                                                    <span style="color: #343A40;" class="input-group-text fw-bold" id="tujuan">Tujuan :</span>
                                                    <textarea type="input" class="form-control" name="indikatorwhat" disabled placeholder="" aria-label="Tujuan KPI" aria-describedby="tujuan"><?=$res['p_what']?></textarea>
                                                </div>
                                                
                                                <div class="input-group mb-3">
                                                    <span style="color: #343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                    <select required class="form-control" name="nilaisi" id="nilaisi">
                                                        <option selected disabled>Pilih Nilai</option>
                                                        <?php 
                                                        // Ambil indikator untuk what ini
                                                        $id_what = $res['id_what'];
                                                        $sql_indikator = "SELECT * FROM tb_indikator_whats 
                                                                        WHERE id_what = '$id_what' 
                                                                        ORDER BY urutan ASC";
                                                        $result_indikator = mysqli_query($conn, $sql_indikator);
                                                        
                                                        while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                                                            // Format: "id_indikator" untuk value, tampilkan keterangan dan nilai
                                                            echo '<option value="'.$indikator['id_indikator'].'">';
                                                            echo ''.$indikator['keterangan'].' = '.$indikator['nilai'];
                                                            echo '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="nilai_what" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php } ?>
                    </table>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
            <!-- ======================================= -->
            <div class="card mb-4 w-50" style="margin-left:7px;">
                <div style="height: 50px; margin-top: -3px;" class="card-header bg-success">
                    <h5 style="color:white;" class="card-title"><?= $poin2; ?></h5>
                    <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">
                        Bobot :
                        <?= $bobot2 ?>%
                    </h5>
                    <div class="card-tools">
                        <button style="color: white; margin-top: -20px; margin-right: 5px;" type="button"
                            data-bs-toggle="modal" data-bs-target="#EditModal2<?= $idKPI ?>" class="btn btn-tool">
                            <i class="bi bi-pencil fs-6"></i>
                        </button>
                        <button style="color: white; margin-top: -20px; margin-right: 5px; " type="button"
                            data-bs-toggle="dropdown" class="btn btn-tool dropdown-toggle">
                            <i class="bi bi-plus-circle fs-6"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#HowModal<?= $idKPI ?>">Tambah
                                How </a>
                        </div>
                        <button style="color: white;" type="button" class="btn btn-tool"
                            data-lte-toggle="card-collapse">
                            <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                            <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Hows</th>
                                <th style="width: 30%">Hasil</th>
                                <th style="width: 5%">
                                    <center>Nilai</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Bobot</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Total</center>
                                </th>
                                <th style="width: 9%">
                                    <center>Action</center>
                                </th>
                            </tr>
                        </thead>
                        <?php
                        $sql1 = "SELECT * FROM tb_hows WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                        $ql = mysqli_query($conn, $sql1);
                        while ($res = mysqli_fetch_assoc($ql)) {
                            ?>
                            <tbody>
                                <tr class="align-middle">
                                    <td><?= $res['p_how']; ?></td>
                                    <td><?= $res['hasil']; ?></td>
                                    <td>
                                        <center><?= $res['nilai']; ?>
                                    </td>
                                    <td>
                                        <center><?= $res['bobot']; ?>%
                                    </td>
                                    <td>
                                        <center><?= $res['total']; ?>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                            <i class="bi bi-eye fs-8"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                                            <a value="<?php echo $res['id_how']; ?>" name="how_edit" class="dropdown-item"
                                                data-bs-toggle="modal"
                                                data-bs-target="#EditHowModal<?= $res['id_how'] ?>">Edit</a>
                                            <a class="dropdown-item" data-bs-toggle="modal"
                                                data-bs-target="#HapusHowModal<?= $res['id_how'] ?>">Hapus</a>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <?php include('pages/kpi/k_modalEdithow.php'); ?>
                            <?php include('pages/kpi/k_modalHapushow.php'); ?>
                        <?php } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('pages/kpi/k_modalWhat.php'); ?>
<?php include('pages/kpi/k_modalHow.php'); ?>
<?php include('pages/kpi/k_modalEditPoin.php'); ?>
<?php include('pages/kpi/k_modalEditPoin2.php'); ?>

  