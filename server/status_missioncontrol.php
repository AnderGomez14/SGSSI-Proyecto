<?php
if (file_exists("green.json")) {
    if (file_exists("candidato.json")) {
        $json = json_decode(file_get_contents('candidato.json'), true);
        die('Se esta verificando un <a href="./' . $json["archivo"] . '">candidato</a>');
    }
    die('Bien, estamos trabajando en ello. ⛏️');
}
if (file_exists("candidato.json")) {
    $json = json_decode(file_get_contents('candidato.json'), true);
    die('Lo tenemos! <a href="./' . $json["archivo"] . '">enlace</a>');
}if (file_exists("hash-rates.json")) {
    die('Esperando... 😐');
}
