<?php

$json = json_decode(file_get_contents(getcwd().'/json/hash-rates.json'), true);
$time = time();
$active_miners = array();

foreach ($json["hashes"] as &$valor) {
    //if (true) {
    if ($time - $valor["heartbeat"] < 14) { // 4 segundos de margen
        if ($valor["key"] == $_POST['key']) {
            $active_miners[$valor["key"]] = 1;
        } else {
            $active_miners[$valor["key"]] = 0;
        }
    }
}
if (count($active_miners) <= 1) {
    unlink(getcwd()."/json/green.json");
}

$target_dir = "./";
$filename = basename($_FILES["upload_file"]["name"]);
$target_file = $target_dir . basename($_FILES["upload_file"]["name"]);
move_uploaded_file($_FILES["upload_file"]["tmp_name"], $target_file);

$candidate_json = array();
$candidate_json["nonce"] = intval($_POST['nonce']);
$candidate_json["finder"] = $_POST['key'];
$candidate_json["archivo"] = $filename;
$candidate_json["consenso"] = $active_miners;
file_put_contents(getcwd().'/json/candidato.json', json_encode($candidate_json));
echo ("candidato_registrado");
