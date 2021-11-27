import hashlib
import sys
import time
import requests
from multiprocessing import Process, Value, cpu_count
import json


HOST = "http://localhost/server/"


def mine(input, output, id, numceros, found, contador):
    with open(input, "rb") as f:
        bytes_file = f.read()
    prefijo = "0"*int(numceros)
    #prefijo = "fabada"
    cont = contador
    while True:
        hash_i = cont.to_bytes(4, 'big').hex() + " " + id
        hash = hashlib.sha256(bytes_file + str.encode(hash_i)).hexdigest()
        if hash.startswith(prefijo):
            f = open(output, "wb")
            f.write(bytes_file + str.encode(hash_i))
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
    if mode == "HRT":
        thread_number = 1
        iterations = 3000
    elif mode == "Mazepin":
        thread_number = cpu_count()//2
        iterations = 10000
    else:
        thread_number = cpu_count()
        iterations = 10000
    start_time = time.time()
    found = Value('f', 1)
    timmys = [Process(target=bench, args=(input, "vadillo_goes_brrrrrr", 10, found, 0+((16**8)//thread_number)*i, iterations))
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


def pool(id):
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
        HOST+"henlo.php", data={'id': int(id), 'hr_HRT': hr_HRT, 'hr_Maz': hr_Maz, 'hr_Par': hr_Par})
    if (presentacion.text != "Bienvenido_al_soviet"):
        print("Something went wrong")
        return
    print("Pool notificada")

    while True:
        status = requests.post(
            HOST+"status.php", data={'id': int(id)})
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
                HOST+"status.php", data={'id': int(id)})
            if status.text != "Estamos_trabajando_en_ello":
                status = json.loads(status.text)
                if("nonce" in status):
                    if(verify(input, id_pool, numceros, status["nonce"])):
                        status = requests.post(
                            HOST+"check.php", data={'id': int(id), 'vote': 1})
                        break
                    else:
                        status = requests.post(
                            HOST+"check.php", data={'id': int(id), 'vote': -1})
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
            HOST+"winner.php", data={'id': int(id), "nonce": nonce}, files=files)

    for t in timmys:
        t.terminate()


if __name__ == '__main__':
    if(len(sys.argv) == 1 or sys.argv[1] == "-h"):
        print(
            "uso: python mine.py -m [archivo_entrada] [archivo_salida] [id] [num_ceros] [numero_donde_empezar] [numero_acabar]\n")
    elif(len(sys.argv) == 3 and sys.argv[1] == "-b"):
        benchmark(sys.argv[2], "b")
    elif(len(sys.argv) == 8 and sys.argv[1] == "-m"):
        daddy(sys.argv[2], sys.argv[3], sys.argv[4],
              sys.argv[5], sys.argv[6], sys.argv[7])
    elif(len(sys.argv) == 6 and sys.argv[1] == "-m"):
        daddy(sys.argv[2], sys.argv[3], sys.argv[4],
              sys.argv[5], 0, 16**8)
    elif(len(sys.argv) == 3 and sys.argv[1] == "-p"):
        if sys.argv[2].isnumeric:
            pool(sys.argv[2])
        else:
            print("ID no valido")
    else:
        print("Numero de argumentos incorrecto")
