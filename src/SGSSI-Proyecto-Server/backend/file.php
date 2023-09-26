<?php
$json = json_decode(file_get_contents('archivo.json'), true);
header('Location:' . $json["archivo"]);
