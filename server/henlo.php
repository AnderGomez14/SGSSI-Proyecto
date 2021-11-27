<?php

$file = 'hash-rates.json';
if (file_exists($file)) {
    $json = json_decode(file_get_contents($file), true);
} else {
    $json = array();
    $json["hashes"] = array();
}

$id = (int) $_POST["id"];
$hr_HRT = (int) $_POST["hr_HRT"];
$hr_Maz = (int) $_POST["hr_Maz"];
$hr_Par = (int) $_POST["hr_Par"];

$updated = false;
foreach ($json["hashes"] as &$valor) {
    if ($valor['id'] == $id) {
        $valor['hr_HRT'] = $hr_HRT;
        $valor['hr_Maz'] = $hr_Maz;
        $valor['hr_Par'] = $hr_Par;
        $valor['heartbeat'] = time();
        $updated = true;
        break;
    }
}
if (!$updated) {
    array_push($json["hashes"], array("id" => $id, "hr_HRT" => $hr_HRT, "hr_Maz" => $hr_Maz, "hr_Par" => $hr_Par, "heartbeat" => time()));
}

$json["hashes"] = array_values($json["hashes"]);
file_put_contents($file, json_encode($json));
echo ("Bienvenido_al_soviet");
