<?php
$servername = "localhost";
$username = "alexdell";
$password = "alex123";
$dbname = "signature";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

function obtenerDatos() {
    global $conn;
    $sql = "SELECT * FROM images";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    } else {
        return null;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $datos = obtenerDatos();
    if ($datos !== null) {
        header('Content-Type: application/json');
        echo json_encode($datos);
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("message" => "No se encontraron datos."));
    }
}

$conn->close();
?>
