<?php
if (!$security_test) exit;

if (!is_logged_in()) {
    header("Location: login.php");
    exit;
}

if ($_GET['tip'] == delete_sifaris) {
    $id = addslashes($_GET['cid']);
    $db_link->where('id', $id)->delete('sifaris');
    echo '<script>document.location.href="?menu=sifaris";</script>';
    exit;
}

if (empty($_GET['tip'])) {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-primary mb-3">
                <div class="card-header"> GET A QUOTE  </div>
                <div class="card-body">
                    <div id="response"></div>
                    <div class="dataTable_wrapper">
                        <table class="table table-striped table-bordered table-hover" id="listSifaris">
                            <thead>
                            <tr>
                                <td width="5px"><strong>id </strong></td>
                                <td width="50px"><strong>Ad Soyad</strong></td>
                                <td width="50px"><strong>Email Phone</strong></td>
                                <td width="50px"><strong>Subject Position</strong></td>
                                <td width="15%"><strong>Qeyd Tarix</strong></td>
                                <td width="10%"><strong>Service</strong></td>
                                <td width="10"><strong>Del</strong></td>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sifaris_info = $db_link->get('sifaris');
                            foreach ($sifaris_info as $line) {
                                $nomre = $line["id"];
                                $tarix = $line["tarix"];
                                $sid = stripslashes($line["sid"]);
                                $ad = $line["ad"];
                                $soyad = $line["soyad"];
                                $email = $line["email"];
                                $subject = $line["subject"];
                                $position = $line["position"];
                                $phone = $line["phone"];
                                $qeyd = $line["qeyd"];
                                $tarix = $line["tarix"];
                                $industry = $db_link->where("id", $line["industry"])->getValue("services_industry", "title_az");
                                $service = $db_link->where("id", $line["service"])->getValue("services_service", "title_az");
                                $solution = $db_link->where("id", $line["solution"])->getValue("services_solution", "title_az");
                                
                                
                                $del = '<a onclick="Del(\'index.php?menu=sifaris&tip=delete_sifaris&cid=' . $nomre . '\')" href="JavaScript:;">Delete</a> ';
                                print "<tr>
                                    <td>$nomre</td>
                                    <td>$ad $soyad</td>
                                    <td>$email<hr>$phone</td>
                                    <td>$subject<hr>$position</td>
                                    <td>$qeyd<hr>$tarix</td>
                                    <td>$industry<hr>$service<hr>$solution</td>
                                    <td>$del</td>
                                    </tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>