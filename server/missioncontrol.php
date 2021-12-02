<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Mission Control 🚀</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="main.js"></script>
    <style>
        #miners {
            font-family: Arial, Helvetica, sans-serif;
            border-collapse: collapse;
        }

        #miners td,
        #miners th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        #miners tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        #miners tr:hover {
            background-color: #ddd;
        }

        #miners th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #04AA6D;
            color: white;
        }
    </style>
</head>

<body>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        Fichero a minar
        <input type="file" name="fileToUpload" id="fileToUpload">
        <input type="submit" name="submit">
    </form>
    <div style="display: flex;
    align-content: center;
    justify-content: center;">
        <table id="miners">

        </table>
        <div style="    margin-left: 2%;
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: space-evenly
    ">
            <div>
                <label for="cpu_mode">Modo de CPU:</label>
                <select id="cpu_mode" name="cpu_mode">
                    <option value="HRT">CPU Friendly</option>
                    <option value="Mazepin">CPU no tan Friendly</option>
                    <option value="Maldonado">CPU Party 🔥</option>
                </select>
            </div>
            <div>
                <label for="ceros">Numero de ceros:</label>
                <input type="number" id="ceros" name="ceros">
            </div>
            <div>
                <label for="id">ID de la pool:</label>
                <input type="text" id="id" name="id">
            </div>
            <button style="width: 100%;" type="button" onclick='$.get( "clear.php", { modo: "reset"} );'>Reset 🔄️</button>
            <button style="width: 100%;" type="button" onclick='$.get( "clear.php" );'> STOP THE COUNT! 🚩</button>
            <button style="width: 100%;" type="button" onclick="tminus0()">LAUNCH 🚀</button>
        </div>
    </div>
    <div style="
    display: flex;
    justify-content: center;
    margin: 5%;
    text-align: center;
    ">
        <div id="estado"></div>
    </div>
</body>

</html>