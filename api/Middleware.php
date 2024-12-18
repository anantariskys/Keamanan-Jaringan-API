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

    // Middleware untuk limitasi jumlah request dalam periode waktu tertentu
    public function rateLimit() {
        $ip = $_SERVER['REMOTE_ADDR'];  // Ambil IP pengguna
        $time = time();  // Waktu sekarang
        $rateLimitFile = "rate_limit_$ip.txt";  // Nama file berdasarkan IP

        // Cek apakah file rate limit sudah ada
        if (file_exists($rateLimitFile)) {
            $data = file_get_contents($rateLimitFile);  // Baca data dari file
            $data = explode(",", $data);  // Pisahkan waktu dan jumlah request

            // Validasi data apakah formatnya benar
            if (count($data) == 2) {
                $lastRequestTime = $data[0];  // Waktu request terakhir
                $requestCount = (int)$data[1];  // Jumlah request yang telah dilakukan

                // Jika request terakhir dalam 60 detik yang lalu
                if ($lastRequestTime >= $time - 10) {
                    // Jika sudah melebihi batas 10 request
                    if ($requestCount >= 5) {
                        header("HTTP/1.1 429 Too Many Requests");
                        echo json_encode([
                            "status" => "error",
                            "message" => "Rate limit exceeded. Try again later."
                        ]);
                        exit();
                    } else {
                        // Jika belum melebihi batas, tambah jumlah request
                        $data[1]++;
                        file_put_contents($rateLimitFile, implode(",", $data));  // Update file
                    }
                } else {
                    // Jika sudah lebih dari 60 detik, reset data
                    file_put_contents($rateLimitFile, "$time,1");
                }
            } else {
                // Jika format data tidak sesuai, reset data
                file_put_contents($rateLimitFile, "$time,1");
            }
        } else {
            // Jika file rate limit tidak ada, buat baru dengan data pertama
            file_put_contents($rateLimitFile, "$time,1");
        }
    }
}
?>
