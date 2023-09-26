import hashlib
import sys
import time
import os
import requests
from multiprocessing import Process, Value, cpu_count
import json


HOST = "http://localhost:8000/api/"

def mine(input, output, id, numceros, found, contador):
    with open(input, "rb") as f:
        bytes_file = f.read()
    prefijo = "0"*int(numceros)
    cont = contador
    while True:
        hash_i = cont.to_bytes(4, 'big').hex() + " " + id
        hash = hashlib.sha256(bytes_file + str.encode(hash_i)).hexdigest()
        if hash.startswith(prefijo):
            f = open(output, "wb")
            f.write(bytes_file + str.encode(hash_i))
            f.close()
            f = open("nonce.txt", "w")
            f.write(hash_i.split()[0])
            f.close()
            break
        cont += 1
    found.value = 0
    print(hash)


def daddy(input, output, id, numceros, start, finish):
    found = Value('f', 1)
    total = finish - start
    timmys = [Process(target=mine, args=(input, output, id, numceros, found, start+(total//cpu_count())*i))
              for i in range(cpu_count())]
    for t in timmys:
        t.start()
    while found.value == 1:
        time.sleep(1)
    for t in timmys:
        t.terminate()


def bench(input, id, numceros, found, contador, target):
    with open(input, "rb") as f:
        bytes_file = f.read()
    prefijo = "0"*int(numceros)
    cont = 0
    while cont < target:
        try:
            hash_i = contador.to_bytes(4, 'big').hex() + " " + id
        except:
            print("ERROR: "+cont)
            break
        hash = hashlib.sha256(bytes_file + str.encode(hash_i)).hexdigest()
        if hash.startswith(prefijo):
            break
        cont += 1
        contador += 1
    found.value += cont


def benchmark(input, mode):
    #Cogemos el numero de bytes del archivo
    size = os.path.getsize(input)
    if mode == "HRT":
        thread_number = 1
        iterations = 500000 * (2300/size) + 1
    elif mode == "Mazepin":
        thread_number = cpu_count()//2
        iterations = 800000 * (2300/size) + 1
    else:
        thread_number = cpu_count()
        iterations = 1200000 * (2300/size) + 1
    start_time = time.time()
    found = Value('f', 1)
    timmys = [Process(target=bench, args=(input, "pool_goes_brrrrrr", 10, found, 0+((16**8)//thread_number)*i, iterations))
              for i in range(thread_number)]
    for t in timmys:
        t.start()
    for t in timmys:
        t.join()
    hr = int((found.value-1)/(time.time()-start_time))
    print("El hash-rate es de: " +
          str(hr) + "H/s")
    return hr


def verify(input, id, numceros, nonce):
    with open(input, "rb") as f:
        bytes_file = f.read()
    prefijo = "0"*int(numceros)

    hash_i = nonce.to_bytes(4, 'big').hex() + " " + id

    hash = hashlib.sha256(bytes_file + str.encode(hash_i)).hexdigest()
    if hash.startswith(prefijo):
        print("Se ha validado el nonce "+str(nonce)+" recibido: " + hash)
        return True
    else:
        print("El hash recibido es trucha, Seguimos trabajando")
        return False


def pool(idMiner):
    print("Descargando archivo a minar")
    r = requests.get(HOST+"file.php", allow_redirects=True)
    input = r.url.split("/").pop()
    output = "mined_"+input
    open(input, 'wb').write(r.content)
    print("Descargado!")
    print("Midiendo el Hash-Rate en modo CPU Friendly")
    hr_HRT = benchmark(input, "HRT")
    print("Midiendo el Hash-Rate en modo CPU no tan Friendly")
    hr_Maz = benchmark(input, "Mazepin")
    print("Midiendo el Hash-Rate en modo CPU Party")
    hr_Par = benchmark(input, "Party")

    print("Notificando a la pool...")
    presentacion = requests.post(
        HOST+"henlo.php", data={'id': int(idMiner), 'hr_HRT': hr_HRT, 'hr_Maz': hr_Maz, 'hr_Par': hr_Par, 'poolKey': poolKey})
    if (presentacion.text == "BAD_POOL_KEY"):
        print("Clave de la pool incorrecta")
        return
    # Si no es JSON la respuesta
    if (presentacion.text[0] != "{"):
        print("ERROR")
        return
    presentacion = json.loads(presentacion.text)
    key = presentacion["key"]
    print("Pool notificada")


    while True:
        status = requests.post(
            HOST+"status.php", data={'key': key})
        if status.text == "Estamos_trabajando_en_ello":
            print("Esperando para el green green green")
            time.sleep(10)
        else:
            break

    status = json.loads(status.text)

    if status["cpu_mode"] == "HRT":
        thread_number = 1
        mensaje = "CPU Friendly"
    elif status["cpu_mode"] == "Mazepin":
        thread_number = cpu_count()//2
        mensaje = "CPU no tan Friendly"
    else:
        thread_number = cpu_count()
        mensaje = "CPU Party"

    total = int(status["end"]) - int(status["start"])
    stake = str(round((total / 16**8)*100, 1))

    print("La pool ha elegido los siguientes ajustes: " +
          mensaje+" con " + str(status["numceros"])+" ceros. Tu stake corresponde al " + stake + "%")

    found = Value('f', 1)
    id_pool = status["id_pool"]
    numceros = int(status["numceros"])
    timmys = [Process(target=mine, args=(input, output, id_pool, numceros, found, status["start"]+(total//thread_number)*i))
              for i in range(thread_number)]
    for t in timmys:
        t.start()
    print("GREEN GREEN GREEN!")

    i = 0
    while found.value == 1:
        if i == 10:
            i = 0
            status = requests.post(
                HOST+"status.php", data={'key': key})
            if status.text != "Estamos_trabajando_en_ello":
                status = json.loads(status.text)
                if("nonce" in status):
                    if(verify(input, id_pool, numceros, status["nonce"])):
                        status = requests.post(
                            HOST+"check.php", data={'key': key, 'vote': 1})
                        break
                    else:
                        status = requests.post(
                            HOST+"check.php", data={'key': key, 'vote': -1})
                        time.sleep(5)
                else:
                    print("Estamos trabajando en ello")
            else:
                break

        time.sleep(1)
        i += 1

    print("WINNER WINNER CHICKEN DINNER!")
    if (found.value == 0):
        f = open("nonce.txt", "r")
        nonce = int(f.read(), 16)
        f.close()
        files = {'upload_file': open(output, 'rb')}
        requests.post(
            HOST+"winner.php", data={'key': key, "nonce": nonce}, files=files)

    for t in timmys:
        t.terminate()


if __name__ == '__main__':
    if(len(sys.argv) == 1 or sys.argv[1] == "-h"):
        print(
            "uso: python mine_client.py -p [id] -k [poolKey]\n")
    elif(len(sys.argv) == 5 and sys.argv[1] == "-p" and sys.argv[3] == "-k"):
        global poolKey
        poolKey = sys.argv[4]
        if sys.argv[2].isnumeric:
            pool(sys.argv[2])
        else:
            print("ID no valido")
    else:
        print("Numero de argumentos incorrecto")
