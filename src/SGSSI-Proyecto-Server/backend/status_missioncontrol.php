<?php
if (file_exists(getcwd()."/json/green.json")) {
    if (file_exists(getcwd()."/json/candidato.json")) {
        $json = json_decode(file_get_contents(getcwd().'/json/candidato.json'), true);
        die('Se esta verificando un <a href="./' . $json["archivo"] . '">candidato</a>');
    }
    die('Bien, estamos trabajando en ello. ⛏️');
}
if (file_exists(getcwd()."/json/candidato.json")) {
    $json = json_decode(file_get_contents(getcwd().'/json/candidato.json'), true);
    die('Lo tenemos! <a href="./' . $json["archivo"] . '">enlace</a>');
}if (file_exists(getcwd()."/json/hash-rates.json")) {
    die('Esperando... 😐');
}
