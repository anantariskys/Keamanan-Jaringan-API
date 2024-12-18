<?php
require_once 'Auth.php';
require_once 'Middleware.php';
require_once 'Database.php';

class Routes {
    public function login($username, $password) {
        $auth = new Auth();
        try {
            $jwt = $auth->login($username, $password);
            echo json_encode(['token' => $jwt]);
        } catch (Exception $e) {
            header("HTTP/1.1 401 Unauthorized");
            echo json_encode(['message' => $e->getMessage()]);
        }
    }

    // Endpoint Profil
    public function getProfile($jwt) {
        $auth = new Auth();
        try {
            $decoded = $auth->checkLogin($jwt);
         
            echo json_encode([
                'nim' => $decoded['data']['nim'],
                'name' => $decoded['data']['nama']
            ]);
        } catch (Exception $e) {
            echo json_encode(['message' => 'Invalid Token']);
        }
    }

    // Endpoint Records
    public function getRecords($jwt) {
        $auth = new Auth();
        try {
            $decoded = $auth->checkLogin($jwt);
            $database = new Database();
            $db = $database->getConnection();
            $stmt = $db->prepare("SELECT id, ipk, semester ,date FROM reports WHERE student_nim = :user_id");
            $stmt->bindParam(':user_id', $decoded['data']['nim']);
            $stmt->execute();
            $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
         
            echo json_encode([
                'status' => 'success',
                'data' => $records
            ]);
        } catch (Exception $e) {
            echo json_encode(['me' => $e]);
        }
    }

    public function changePassword($jwt, $password) {
        $auth = new Auth();
        try {
            $decoded = $auth->checkLogin($jwt);
            $database = new Database();
            $db = $database->getConnection();
            $stmt = $db->prepare("UPDATE students SET password = :password WHERE nim = :nim");
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $hashedPassword);
            $stmt->bindParam(':nim', $decoded['data']['nim']);
            $stmt->execute();
            echo json_encode([
                'status' => 'success',
                'message' => 'Password changed successfully'
            ]);
        } catch (Exception $e) {
            echo json_encode(['message' => $e->getMessage()]);
        }
    }
}
?>
