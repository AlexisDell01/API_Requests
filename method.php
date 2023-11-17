<?php
require "config/Conexion.php";

switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        $sql = "SELECT id, name, email, comment, date FROM comments";
        $query = ejecutarConsulta($sql);

        if ($query && $query->num_rows > 0) {
            $data = array();
            while ($row = $query->fetch_assoc()) {
                $data[] = $row;
            }
            // Devolver los resultados en formato JSON
            header('Content-Type: application/json');
            echo json_encode($data);
        } else {
            echo "No se encontraron registros en la tabla.";
        }

        $conexion->close();

        break;


      case 'POST':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recibir los datos del formulario HTML
            $name = $_POST['name'];
            $email = $_POST['email'];
            $comment = $_POST['comment'];
        
            // Insertar los datos en la tabla
            $sql = "INSERT INTO comments (name, email, comment, date) VALUES ('$name', '$email', '$comment', NOW())"; // Reemplaza con el nombre de tu tabla
        
            if ($conexion->query($sql) === TRUE) {
                echo "Datos insertados con éxito.";
            } else {
                echo "Error al insertar datos: " . $conexion->error;
            }
        } else {
            echo "Esta API solo admite solicitudes POST.";
        }
        break;

        case 'PATCH':
            $input_data = file_get_contents("php://input");
            $boundary = substr($input_data, 0, strpos($input_data, "\r\n"));
        
            $parts = array_slice(explode($boundary, $input_data), 1);
            $data = array();
        
            foreach ($parts as $part) {
                if ($part == "--\r\n") break;
                $part = ltrim($part, "\r\n");
                list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);
        
                $raw_headers = explode("\r\n", $raw_headers);
                $headers = array();
                foreach ($raw_headers as $header) {
                    list($name, $value) = explode(':', $header);
                    $headers[strtolower($name)] = ltrim($value, ' ');
                }
        
                if (isset($headers['content-disposition'])) {
                    $filename = null;
                    $tmp_name = null;
                    preg_match(
                        '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                        $headers['content-disposition'],
                        $matches
                    );
                    list(, $type, $name) = $matches;
                    isset($matches[4]) and $filename = $matches[4];
        
                    if ($filename === null) {
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                    }
                }
            }
        
            $id = isset($data['comment_id']) && $data['comment_id'] !== '' ? $data['comment_id'] : null;
            $name = isset($data['name']) && $data['name'] !== '' ? $data['name'] : null;
            $email = isset($data['email']) && $data['email'] !== '' ? $data['email'] : null;
            $comment = isset($data['comment']) && $data['comment'] !== '' ? $data['comment'] : null;
        
            echo "ID: " . $id . "\n";
            echo "Name: " . $name . "\n";
            echo "Email: " . $email . "\n";
            echo "Comment: " . $comment . "\n";
        
            if ($id !== null) {
                // Utilizar sentencias preparadas para prevenir inyecciones SQL
                $update_fields = array();
                $update_values = array();
        
                if ($name !== null) {
                    $update_fields[] = 'name = ?';
                    $update_values[] = $name;
                }
                if ($email !== null) {
                    $update_fields[] = 'email = ?';
                    $update_values[] = $email;
                }
                if ($comment !== null) {
                    $update_fields[] = 'comment = ?';
                    $update_values[] = $comment;
                }
        
                if (!empty($update_fields)) {
                    $update_query = "UPDATE comments SET " . implode(', ', $update_fields) . " WHERE id = ?";
                    $stmt = $conexion->prepare($update_query);
        
                    // Construir array de referencias para los parámetros de bind_param
                    $params = array();
                    $params[] = implode('', array_fill(0, count($update_values), 's')) . 'i'; // Tipos de parámetros
        
                    foreach ($update_values as &$value) {
                        $params[] = &$value; // Agregar referencias a los valores a actualizar
                    }
        
                    $params[] = &$id; // Agregar referencia al ID
        
                    // Llamar a bind_param con el array de parámetros por referencia
                    call_user_func_array(array($stmt, 'bind_param'), $params);
        
                    if ($stmt->execute()) {
                        echo "Datos actualizados con éxito.";
                    } else {
                        echo "Error al actualizar datos: " . $stmt->error;
                    }
        
                    $stmt->close();
                } else {
                    echo "No hay campos para actualizar.";
                }
            } else {
                echo "Falta el identificador del comentario en la solicitud PATCH.";
            }
            break;

        case 'PUT':
            $input_data = file_get_contents("php://input");
            $boundary = substr($input_data, 0, strpos($input_data, "\r\n"));
        
            $parts = array_slice(explode($boundary, $input_data), 1);
            $data = array();
        
            foreach ($parts as $part) {
                if ($part == "--\r\n") break;
                $part = ltrim($part, "\r\n");
                list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);
        
                $raw_headers = explode("\r\n", $raw_headers);
                $headers = array();
                foreach ($raw_headers as $header) {
                    list($name, $value) = explode(':', $header);
                    $headers[strtolower($name)] = ltrim($value, ' ');
                }
        
                if (isset($headers['content-disposition'])) {
                    $filename = null;
                    $tmp_name = null;
                    preg_match(
                        '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                        $headers['content-disposition'],
                        $matches
                    );
                    list(, $type, $name) = $matches;
                    isset($matches[4]) and $filename = $matches[4];
        
                    if ($filename === null) {
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                    }
                }
            }
        
            $id = isset($data['comment_id']) && $data['comment_id'] !== '' ? $data['comment_id'] : null;
            $name = isset($data['name']) && $data['name'] !== '' ? $data['name'] : null;
            $email = isset($data['email']) && $data['email'] !== '' ? $data['email'] : null;
            $comment = isset($data['comment']) && $data['comment'] !== '' ? $data['comment'] : null;
        
            echo "ID: " . $id . "\n";
            echo "Name: " . $name . "\n";
            echo "Email: " . $email . "\n";
            echo "Comment: " . $comment . "\n";
        
            if ($id !== null && $name !== null && $email !== null && $comment !== null) {
                // Utilizar sentencias preparadas para prevenir inyecciones SQL
                $stmt = $conexion->prepare("UPDATE comments SET name = ?, email = ?, comment = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $comment, $id);
        
                if ($stmt->execute()) {
                    echo "Datos actualizados con éxito.";
                } else {
                    echo "Error al actualizar datos: " . $stmt->error;
                }
                $stmt->close();
            } else {
                echo "Faltan datos en la solicitud PUT.";
            }
            break;

    case 'DELETE':
      if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        // Procesar solicitud DELETE
        $id = $_GET['id'];
        $sql = "DELETE FROM comments WHERE id = $id";
    
        if ($conexion->query($sql) === TRUE) {
            echo "Registro eliminado con éxito.";
        } else {
            echo "Error al eliminar registro: " . $conexion->error;
        }
    } else {
        echo "Método de solicitud no válido.";
    }
    $conexion->close();
      break;

      case 'OPTIONS':
        // Enable CORS for any origin
        header("Access-Control-Allow-Origin: *");
        // Allow specific HTTP methods
        header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE, HEAD, TRACE, PATCH");
        // Allow custom headers
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
        // Allow credentials
        header("Access-Control-Allow-Credentials: true");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            // Respond to the OPTIONS request without processing anything else
            http_response_code(200);
            exit;
        }
        break;

    case 'HEAD':
        if ($_SERVER['REQUEST_METHOD'] === 'HEAD') {
            // Set response headers
            header('Content-Type: application/json');
            header('Custom-Header: PHP 8, HTML');
            // You can set other necessary headers here
            // No need to send a body in a HEAD request, so nothing is printed here.
        } else {
            http_response_code(405); // Method Not Allowed
            echo 'Invalid request method';
        }
        break;

    case 'TRACE':
        header("Access-Control-Allow-Origin: *");
        if ($_SERVER['REQUEST_METHOD'] === 'TRACE') {
            $response = "TRACE request received. Status: 200 OK";
        } else {
            $response = "Invalid request method. Status: 405 Method Not Allowed";
        }
        echo $response;
        break;

    case 'LINK':
        $apiUrl = 'https://example.com/your_endpoint'; // Replace with your API URL
        $resourceUri = '/path/to/your/resource'; // Replace with your resource path
        $linkHeader = '<' . $resourceUri . '>; rel="link-type"'; // Define the Link header

        $options = [
            'http' => [
                'method' => 'LINK',
                'header' => 'Link: ' . $linkHeader,
            ],
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($apiUrl, false, $context);

        if ($response === false) {
            echo "Error sending LINK request.";
        } else {
            echo "LINK request successful. Server response: " . $response;
        }
        break;
        
     default:
       echo 'undefined request type!';
  }
?>