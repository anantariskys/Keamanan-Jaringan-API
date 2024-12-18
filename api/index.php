<?php
require_once 'Routes.php';
require_once 'Middleware.php';

$routes = new Routes();
$middleware = new Middleware();

$middleware->rateLimit();

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestUri = $_SERVER['REQUEST_URI'];

$requestUri = str_replace('/kampus/api', '', $requestUri);


$data = json_decode(file_get_contents("php://input"));

if ($requestUri === '/login' && $requestMethod === 'POST') {
    if (isset($data->nim) && isset($data->password)) {
        $routes->login($data->nim, $data->password);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo json_encode(["message" => "Invalid input. 'nim' and 'password' are required."]);
    }
} else {
    $middleware->authenticate();
    if ($requestUri === '/profile' && $requestMethod === 'GET') {
        $headers = getallheaders();
        $jwt = str_replace('Bearer ', '', $headers['Authorization']);
        $routes->getProfile($jwt);
    } else if ($requestUri === '/records' && $requestMethod === 'GET') {

        $headers = getallheaders();
        $jwt = str_replace('Bearer ', '', $headers['Authorization']);
        $routes->getRecords($jwt);
    } else if ($requestUri === '/change-password' && $requestMethod === 'POST') {
        if (isset($data->password)) {
            $headers = getallheaders();
            $jwt = str_replace('Bearer ', '', $headers['Authorization']);
            $routes->changePassword($jwt, $data->password);
        }
       else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["message" => "Invalid. password are required."]);
        }
        
    } 
    else {
        header("HTTP/1.1 404 Not Found");
        echo json_encode(["message" => "Endpoint Not Found"]);
    }
}
