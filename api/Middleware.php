<?php
class Middleware {
        // Fungsi untuk mengambil header Authorization
        private function getAuthorizationHeader() {
            $headers = getallheaders();
            if (isset($headers['Authorization'])) {
                return $headers['Authorization'];
            }
            return null;
        }
    

    // Middleware untuk autentikasi
    public function authenticate() {
        $authHeader = $this->getAuthorizationHeader();
        if (!isset($authHeader)) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode([
                "status" => "error",
                "message" => "Authorization header is missing."
            ]);
            exit();
        }

   
        $jwt = str_replace('Bearer ', '', $authHeader);
        
        

     
        if (empty($jwt) || $jwt === 'Bearer') {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode([
                "status" => "error",
                "message" => "Authorization token is required."
            ]);
            exit();
        }

       
        $auth = new Auth();
        try {
            $auth->checkLogin($jwt);
        } catch (Exception $e) {
            // Jika token tidak valid atau kadaluarsa
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode([
                "status" => "error",
                "message" => $e->getMessage()
            ]);
            exit();
        }
    }

    

    public function rateLimit() {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'];
        if ($ip === '::1') {
            $ip = '127.0.0.1';
        }
        
 
        $time = time();  
        $rateLimitFile = "rate_limit_$ip.txt";  

        if (file_exists($rateLimitFile)) {
            $data = file_get_contents($rateLimitFile); 
            $data = explode(",", $data);  

  
            if (count($data) == 2) {
                $lastRequestTime = $data[0];  
                $requestCount = (int)$data[1];  

         
                if ($lastRequestTime >= $time - 10) {
                   
                    if ($requestCount >= 5) {
                        header("HTTP/1.1 429 Too Many Requests");
                        echo json_encode([
                            "status" => "error",
                            "message" => "Rate limit exceeded. Try again later."
                        ]);
                        exit();
                    } else {
                        $data[1]++;
                        file_put_contents($rateLimitFile, implode(",", $data));  // Update file
                    }
                } else {
                    file_put_contents($rateLimitFile, "$time,1");
                }
            } else {
                file_put_contents($rateLimitFile, "$time,1");
            }
        } else {
            file_put_contents($rateLimitFile, "$time,1");
        }
    }
}
?>
