<?php

$file = getcwd().'/json/hash-rates.json';
$json = json_decode(file_get_contents($file), true);
foreach ($json["hashes"] as &$valor) {
    if ($valor['key'] == $_POST['key']) {
        $valor['heartbeat'] = time();
        break;
    }
}

$json["hashes"] = array_values($json["hashes"]);
file_put_contents($file, json_encode($json));

if (file_exists(getcwd().'/json/green.json')) {
    if (file_exists(getcwd().'/json/candidato.json')) {
        $json = json_decode(file_get_contents(getcwd().'/json/candidato.json'), true);
        echo json_encode(array("nonce" => $json["nonce"]));
    } else {
        $json = json_decode(file_get_contents(getcwd().'/json/green.json'), true);

        foreach ($json["splits"] as &$valor) {
            if ($valor['key'] == $_POST['key']) {
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
