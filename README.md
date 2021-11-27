# SGSSI-21.Pool

Pool para minar bloques (y otros archivos) de la asignatura SGSSI.

## Minero

Puedes conectarte a la pool haciendo:

```shell
> cd <project location>/client
> pip install requests
> python python mine_client.py -p [id]
```

Si se desea minar un fichero grande (por ejemplo: PDF), se puede usar `mine_client_big.py` en vez de `mine_client.py`.

## Servidor pool

Para poder desplegar el servidor, hace falta un servidor compatible con PHP (Apache funciona bien).

Se despliega la carpeta `server` en el servidor y se accede a `missioncontrol.php`.

Despues poner la URL del servidor en la variable `HOST` de `mine_client.py` y `mine_client_big.py`
