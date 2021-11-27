<?php

$target_dir = "./";
$filename = basename($_FILES["fileToUpload"]["name"]);
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
// Check if image file is a actual image or fake image
if (isset($_POST["submit"])) {
    $json = json_decode(file_get_contents('archivo.json'), true);
    unlink($json["archivo"]);
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);
    $json["archivo"] = $filename;
    file_put_contents('archivo.json', json_encode($json));
    header('Location: missioncontrol.php');

}
