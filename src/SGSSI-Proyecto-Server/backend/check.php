<?php
if (file_exists(getcwd().getcwd()."/json/candidato.json")) {
    $json = json_decode(file_get_contents(getcwd().'/json/candidato.json'), true);
    $mayoria = floor(count($json["consenso"]) / 2) + 1;
    $favor = 0;
    $contra = 0;
    $neutro = 0;

    foreach ($json["consenso"] as $k => &$valor) {
        if ($k == $_POST['key']) {
            $valor = intval($_POST['vote']);
        }

        if ($valor == -1) {
            $contra++;
        } elseif ($valor == 0) {
            $neutro++;
        } elseif ($valor == 1) {
            $favor++;
        }

    }

    if ($contra >= $mayoria) {
        unlink(getcwd()."/json/candidato.json");
    } else {
        if ($favor >= $mayoria) {
            unlink(getcwd()."/json/green.json");
        }
        file_put_contents(getcwd().'/json/candidato.json', json_encode($json));
    }
}
