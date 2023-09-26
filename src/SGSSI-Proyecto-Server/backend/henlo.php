<?php

$poolData = file_get_contents(getcwd().'/json/pool.json');
$poolJson = json_decode($poolData, true);
$poolKey = $poolJson['poolKey'];

if ($_POST["poolKey"] != $poolKey) {
    echo "BAD_POOL_KEY";
    die;
}


$hash = getcwd().'/json/hash-rates.json';
if (file_exists($hash)) {
    $json = json_decode(file_get_contents($hash), true);
} else {
    $json = array();
    $json["hashes"] = array();
}

$id = (int) $_POST["id"];
$hr_HRT = (int) $_POST["hr_HRT"];
$hr_Maz = (int) $_POST["hr_Maz"];
$hr_Par = (int) $_POST["hr_Par"];

$updated = false;
$bytes = random_bytes(20);
$key = bin2hex($bytes);
foreach ($json["hashes"] as &$valor) {
    if ($valor['id'] == $id) {
        $valor['hr_HRT'] = $hr_HRT;
        $valor['hr_Maz'] = $hr_Maz;
        $valor['hr_Par'] = $hr_Par;
        $valor['heartbeat'] = time();
        $valor['key'] = $key;
        $updated = true;
        break;
    }
}
if (!$updated) {
    array_push($json["hashes"], array("id" => $id, "hr_HRT" => $hr_HRT, "hr_Maz" => $hr_Maz, "hr_Par" => $hr_Par, "heartbeat" => time(), "key" => $key));
}

$json["hashes"] = array_values($json["hashes"]);
file_put_contents($hash, json_encode($json));
echo json_encode(array("key" => $key));