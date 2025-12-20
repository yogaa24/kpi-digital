<div class="col-lg-8 connectedSortable">
    <div class="card mb-4">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
            <h5 style="color:white;" class="card-title fw-bolder">What</h5>
            <div class="card-tools">
                <button style="color: white;" type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr class="table-secondary">
                        <th style="color: white;" scope="col" class="col-7 bg-primary">
                            <center>Poin</center>
                        </th>
                        <th style="color: white;" scope="col" class="col-1  bg-primary">
                            <center>Bobot</center>
                        </th>
                        <th style="color: white;" scope="col" class="col-1 bg-primary">
                            <center>Penilaian</center>
                        </th>
                        <th style="color: white;" scope="col" class="col-1 bg-primary">
                            <center>NILAI</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalw = 0;
                    $totalbobot = 0;
                    $totalnilai4 = 0;
                    while ($hasil = mysqli_fetch_assoc($result)) {
                        $poin = $hasil['poin'];
                        $bobot = $hasil['bobot'];
                        $dsf = $hasil['id'];

                        $sql3 = "SELECT SUM(total) as total FROM tbar_whats WHERE id_user=$id_user AND id_kpi=$dsf";
                        $result3 = mysqli_query($connarc, $sql3);
                        $row3 = mysqli_fetch_assoc($result3);
                        $totalnilai = $row3['total'];
                        $nilaiw = number_format(($totalnilai * $bobot) / 100, 2);
                        $totalw += number_format($nilaiw, 2);
                        $totalbobot += $bobot;

                        echo "
                                <tr>
                                    <td>$poin</td>
                                    <td><center>$bobot%</center></td>
                                    <td><center>".round($totalnilai)."</center></td>
                                    <td><center>$nilaiw</center></td>
                                </tr>";
                    }

                    $sql4 = "SELECT SUM(total) as total FROM tbar_whats WHERE id_user=$id_user";
                    $result4 = mysqli_query($connarc, $sql4);
                    $row4 = mysqli_fetch_assoc($result4);
                    $totalnilai4 = $row4['total'];
                    ?>

                    <tr class="table-secondary">
                        <th>
                            <center>TOTAL NILAI</center>
                        </th>
                        <th>
                            <center><?= $totalbobot ?> %</center>
                        </th>
                        <th>
                            <center><?= round($totalnilai4,2); ?></center>
                        </th>
                        <th>
                            <center><?= $totalw ?></center>
                        </th>
                    </tr>

                </tbody>

                <tr>
                    <th rowspan="2"></th>
                    <th style="color: white;" rowspan="2" class="align-middle table-secondary bg-primary">
                        <center>WHAT</center>
                    </th>
                    <th class="table-secondary">
                        <center>BOBOT</center>
                    </th>
                    <th class="table-secondary">
                        <center>NILAI</center>
                    </th>
                </tr>
                <tr>
                    <?php
                    $bobotkpiw = 0;
                    $sql5 = "SELECT bobotwhat as bw FROM tbar_bobotkpi WHERE id_user=$id_user";
                    $result5 = mysqli_query($connarc, $sql5);
                    while ($row5 = mysqli_fetch_assoc($result5)) {
                        $bobotkpiw = $row5['bw'];
                    }
                    $zbotw = ($totalw * $bobotkpiw) / 100;
                    ?>

                    <td>
                        <center><?= $bobotkpiw ?> % </center>
                    </td>
                    <td>
                        <center><?= round($zbotw,2) ?></center>
                    </td>
                </tr>
                </tbody>
            </table>
            <!-- --------------------------------------------------------------------->
        </div>

    </div>
    <div class="card mb-4">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-success">
            <h5 style="color:white;" class="card-title fw-bolder">How</h5>
            <div class="card-tools">
                <button style="color: white;" type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-sm">
                <thead>
                    <tr class="table-secondary">
                        <th style="color: white;" scope="col" class="col-7  bg-success">
                            <center>Poin</center>
                        </th>
                        <th style="color: white;" scope="col" class="col-1 bg-success">
                            <center>Bobot</center>
                        </th>
                        <th style="color: white;" scope="col" class="col-1 bg-success">
                            <center>Penilaian</center>
                        </th>
                        <th style="color: white;" scope="col" class="col-1 bg-success">
                            <center>NILAI</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $totalh = 0;
                    $totalboboth = 0;
                    $totalnilai7 = 0;
                    $resultff = mysqli_query($connarc, $sql);

                    while ($hasil = mysqli_fetch_assoc($resultff)) {
                        $poin2 = $hasil['poin2'];
                        $bobot2 = $hasil['bobot2'];
                        $dsf = $hasil['id'];

                        $sql7 = "SELECT SUM(total) as totalh FROM tbar_hows WHERE id_user=$id_user AND id_kpi=$dsf";
                        $result7 = mysqli_query($connarc, $sql7);
                        $row7 = mysqli_fetch_assoc($result7);
                        $totalnilaih = $row7['totalh'];
                        
                        $nilaih = number_format(($totalnilaih * $bobot2) / 100, 2);
                        $totalh += number_format($nilaih,2);
                        $totalboboth += $bobot2;

                        echo "
                                <tr>
                                    <td>$poin2</td>
                                    <td><center>$bobot2%</center></td>
                                    <td><center>".round($totalnilaih)."</center></td>
                                    <td><center>$nilaih</center></td>
                                </tr>";
                    }

                    $sql4 = "SELECT SUM(total) as total FROM tbar_hows WHERE id_user=$id_user ";
                    $result4 = mysqli_query($connarc, $sql4);
                    $row4 = mysqli_fetch_assoc($result4);
                    $totalnilai5 = $row4['total'];
                    ?>

                    <tr class="table-secondary">
                        <th>
                            <center>TOTAL NILAI</center>
                        </th>
                        <th>
                            <center>
                                <?= $totalboboth ?> %
                            </center>
                        </th>
                        <th>
                            <center>
                                <?= round($totalnilai5,2) ?>
                            </center>
                        </th>
                        <th>
                            <center>
                                <?= $totalh ?>
                            </center>
                        </th>
                    </tr>
                    <tr>
                        <th rowspan="2"></th>
                        <th style="color: white;" rowspan="2" class=" bg-success align-middle table-secondary">
                            <center>HOW</center>
                        </th>
                        <th class="table-secondary">
                            <center>BOBOT</center>
                        </th>
                        <th class="table-secondary">
                            <center>NILAI</center>
                        </th>
                    </tr>
                    <tr>
                        <?php
                        $bobotkpih = 0;
                        $sql8 = "SELECT bobothow as bh FROM tbar_bobotkpi WHERE id_user=$id_user";
                        $result8 = mysqli_query($connarc, $sql8);
                        while ($row8 = mysqli_fetch_assoc($result8)) {
                            $bobotkpih = $row8['bh'];
                        }
                        $zboth = ($totalh * $bobotkpih) / 100;
                        ?>

                        <td>
                            <center>
                                <?= $bobotkpih ?>%
                            </center>
                        </td>
                        <td>
                            <center>
                                <?= round($zboth,2) ?>
                            </center>
                        </td>
                    </tr>
                </tbody>
            </table>
            <!-- --------------------------------------------------------------------->
        </div>
    </div>
</div>