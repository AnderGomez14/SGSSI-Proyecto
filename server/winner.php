<?php

$json = json_decode(file_get_contents('hash-rates.json'), true);
$time = time();
$active_miners = array();

foreach ($json["hashes"] as &$valor) {
    //if (true) {
    if ($time - $valor["heartbeat"] < 14) { // 4 segundos de margen
        if ($valor["id"] == $_POST['id']) {
            $active_miners[$valor["id"]] = 1;
        } else {
            $active_miners[$valor["id"]] = 0;
        }
    }
}
if (count($active_miners) <= 1) {
    unlink("green.json");
}

$target_dir = "./";
$filename = basename($_FILES["upload_file"]["name"]);
$target_file = $target_dir . basename($_FILES["upload_file"]["name"]);
move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file);

$candidate_json = array();
$candidate_json["nonce"] = intval($_POST['nonce']);
$candidate_json["finder"] = $_POST['id'];
$candidate_json["archivo"] = $filename;
$candidate_json["consenso"] = $active_miners;
file_put_contents('candidato.json', json_encode($candidate_json));
echo ("candidato_registrado");
