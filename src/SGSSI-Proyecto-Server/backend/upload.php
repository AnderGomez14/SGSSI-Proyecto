<?php
session_start();
$target_dir = "./";
$filename = basename($_FILES["file"]["name"]);
$target_file = $target_dir . basename($_FILES["file"]["name"]);
//Move to a temp file
move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
$FileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
echo $FileType;
if($FileType != "pdf" && $FileType != "txt" ) {
    echo "Sorry, only PDF & TXT files are allowed.";
    unlink($target_file);
    exit;
}
// Verify that is a valid pdf file
if($FileType == "pdf") {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $target_file);
    if ($mime != "application/pdf") {
        echo "Sorry, file is not a valid PDF.";
        unlink($target_file);
        exit;
    }
}

$json = json_decode(file_get_contents('archivo.json'), true);
unlink($json["archivo"]);
$json["archivo"] = $filename;
file_put_contents('archivo.json', json_encode($json));

