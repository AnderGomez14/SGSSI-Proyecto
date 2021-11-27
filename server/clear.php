<?php

unlink("green.json");
if ($_GET["modo"] == "reset") {
    unlink("candidato.json");
    unlink("hash-rates.json");
}
