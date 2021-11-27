<?php
if (count($_POST) != 3) {
    die("ERROR");
}
$file = 'hash-rates.json';
$green_file = 'green.json';
if (file_exists($file)) {
    $json = json_decode(file_get_contents($file), true);
} else {
    die("F");
}

if ($_POST['cpu_mode'] == "HRT") {
    $tag = 'hr_HRT';
} elseif ($_POST['cpu_mode'] == "Mazepin") {
    $tag = 'hr_Maz';
} elseif ($_POST['cpu_mode'] == "Maldonado") {
    $tag = 'hr_Par';
}

$total = 0;
$time = time();
foreach ($json["hashes"] as &$valor) {
    //if (true) {
    if ($time - $valor["heartbeat"] < 14) { // 4 segundos de margen
        $total += $valor[$tag];
    } else {
        $valor['heartbeat'] = 0;
    }
}
$green_json = array();
$green_json["cpu_mode"] = $_POST['cpu_mode'];
$green_json["numceros"] = $_POST['numceros'];
$green_json["id_pool"] = $_POST['id_pool'];

$green_json["splits"] = array();

$i = 0;
foreach ($json["hashes"] as &$valor) {
    if ($valor['heartbeat'] != 0) {
        $seccion = floor(($valor[$tag] / $total) * (16 ** 8));
        $end = $i + $seccion;
        array_push($green_json["splits"], array("id" => $valor['id'], "start" => $i, "end" => $end));
        $i = $end;
    }
}

$green_json["splits"] = array_values($green_json["splits"]);
file_put_contents($green_file, json_encode($green_json));

echo ("GO");
