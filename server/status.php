<?php

$file = 'hash-rates.json';
$json = json_decode(file_get_contents($file), true);
foreach ($json["hashes"] as &$valor) {
    if ($valor['id'] == (int) $_POST['id']) {
        $valor['heartbeat'] = time();
        break;
    }
}

$json["hashes"] = array_values($json["hashes"]);
file_put_contents($file, json_encode($json));

if (file_exists('green.json')) {
    if (file_exists('candidato.json')) {
        $json = json_decode(file_get_contents('candidato.json'), true);
        echo json_encode(array("nonce" => $json["nonce"]));
    } else {
        $json = json_decode(file_get_contents('green.json'), true);

        foreach ($json["splits"] as &$valor) {
            if ($valor['id'] == (int) $_POST['id']) {
                $start = $valor['start'];
                $end = $valor['end'];
                break;
            }
        }
        if (is_null($start)) {
            die("Something_went_wrong");
        }
        echo json_encode(array("start" => $start, "end" => $end, "cpu_mode" => $json["cpu_mode"], "numceros" => $json["numceros"], "id_pool" => $json["id_pool"]));

    }

} else {
    die("Estamos_trabajando_en_ello");
}
