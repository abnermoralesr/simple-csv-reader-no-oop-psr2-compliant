<?php
session_start();
if (isset($_SESSION['token'])) {
    $csv = array();
    $headers = true;
    $errorMessage = "";
    $filesErrorMessage = array(
        1=>"The uploaded file exceeds the upload_max_filesize directive.",
        2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.",
        3=>"The uploaded file was only partially uploaded.",
        4=>"No file was uploaded.",
        6=>"Missing a temporary folder."
    );
    if (isset($_FILES['upload'])) {
        $fielType = $_FILES['upload']['type'];
        switch ($fielType) {
            case "text/csv":
                $fileOk = true;
                break;
            case "text/plain":
                $fileOk = true;
                break;
            case "application/vnd.ms-excel":
                $fileOk = true;
                break;
            case "application/octet-stream":
                $fileOk = true;
                break;
            default:
                $fileOk = false;
                break;
        }
        if ($_FILES['upload']['error'] == 0) {
            $exploded = explode('.', $_FILES['upload']['name']);
            $checkExtension = strtolower(end($exploded));
            $tmpName = $_FILES['upload']['tmp_name'];
            if ($checkExtension === 'csv'&&$fileOk==true) {
                if (($handle = fopen($tmpName, 'r')) !== false) {
                    $items = 0;
                    $firstRow = true;
                    $tBody = "";
                    $tHead = "";
                    while (($data = fgetcsv($handle)) !== false) {
                        $colCount = count($data);
                        if ($firstRow&&$headers==true) {
                            $tHead .="<tr>";
                            for ($i = 0; $i < $colCount; $i++) {
                                $tHead .= "<th>".strtoupper(str_replace("_", " ", $data[$i]))."</th>";
                            }
                            $tHead .="</tr>";
                            $firstRow = false;
                            continue;
                        } else {
                            $tBody .="<tr>";
                            for ($i = 0; $i < $colCount; $i++) {
                                $tBody .= "<td>".$data[$i]."</td>";
                            }
                            $tBody .="</tr>";
                        }
                        $items++;
                    }
                    fclose($handle);
                    if ($items<=0) {
                        $errorMessage = "The file is not a CSV file or is empty, please try again.";
                    }
                } else {
                    $errorMessage = "File could not be opened please try again.";
                }
            } else {
                $errorMessage = "The file is not a CSV file.";
            }
        } else {
            $errorMessage = $filesErrorMessage[$_FILES['upload']['error']];
        }
    } else {
        $errorMessage = "The file is not a CSV file.";
    }
} else {
    $errorMessage = "Token expired, please refresh your page pressing the refresh 
    button of your web browser or F5 for short.";
}
if (empty($errorMessage)) {
    ?>
    <table id="myTable">
        <?php if (!empty($tHead)) { ?>
            <thead>
                <?php echo $tHead; ?>
            </thead>
        <?php } ?>
        <tbody>
            <?php echo $tBody; ?>
        </tbody>
    </table>
    <script>
        $(document).ready( function () {
            var fileName = "Exported CSV";
            var pageCheck = $("#myTable tr > th").length;
            var pageSize = "A4";
            if (pageCheck>10) {
                pageSize = "Legal"
            }
            if (pageCheck>=12) {
                pageSize = "Tabloid"
            }
            $('#myTable').DataTable({
                dom: '<"top"Bflp<"clear">>rt<"bottom"Bifp<"clear">>',
                buttons: [
                    {
                        extend: 'copy',
                    },
                    {
                        extend: 'print',
                    },
                    {
                        extend: 'excelHtml5',
                        title: fileName
                    },
                    {
                        extend: 'pdfHtml5',
                        title: fileName,
                        orientation : 'landscape',
                        pageSize : pageSize,
                        text : '<i class="fa fa-file-pdf-o"> PDF</i>',
                        titleAttr : 'PDF'
                    }
                ],
                "pageLength": 100
            });
        });
    </script>
    <?php
} else {
    echo "<p>".$errorMessage."</p>";
}