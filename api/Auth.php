<?php
class Auth {

    public function generateJWT($userId, $nama) {
        $issuedAt = time();
        $expirationTime = $issuedAt + 360; 
        $payload = array(
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "nim" => $userId,
            "nama" => $nama
         
        );
        return $this->encodeJWT($payload);
    }

    private function encodeJWT($payload) {
        $header = base64_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $body = base64_encode(json_encode($payload));
        $signature = hash_hmac('sha256', "$header.$body", getenv('JWT_SECRET'), true);
        $signature = base64_encode($signature);
        return "$header.$body.$signature";
    }

    public function decodeJWT($jwt) {
        $results = explode('.', $jwt);
        if (count($results) !== 3) {
            throw new Exception('Invalid token format');
        }

        list($header, $body, $signature) = $results;
        
        $decodedBody = json_decode(base64_decode($body), true);
        
    if (!isset($decodedBody['exp']) || time() > $decodedBody['exp']) {
        throw new Exception('Token has expired');
    }

        $validSignature = hash_hmac('sha256', "$header.$body", getenv('JWT_SECRET'), true);
        $validSignature = base64_encode($validSignature);

        if ($validSignature === $signature) {
            return $decodedBody;
        } else {
            throw new Exception('Invalid token signature');
        }
    }

    public function login($nim, $password) {
        if (empty($nim) || empty($password)) {
            throw new Exception("NIM and Password are required.");
        }

        $database = new Database();
        $db = $database->getConnection();

        $query = "SELECT * FROM students WHERE nim = :nim LIMIT 1";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':nim', $nim);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $jwt = $this->generateJWT($user['nim'], $user['name']);
            return [
                "status" => "success",
                "message" => "Login successful.",
                "token" => $jwt
            ];
        } else {
            throw new Exception("Invalid credentials");
        }
    }

    public function checkLogin($jwt) {
        try {
            if (!$jwt) {
                throw new Exception('Authorization token is required');
            }

            $decoded = $this->decodeJWT($jwt);

            return [
                "status" => "success",
                "message" => "Token is valid",
                "data" => $decoded
            ];

        } catch (Exception $e) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
            exit();
        }
    }
}
?>
