<?php
require_once '../middleware/isAuthenticated.php';

require '../db.php';

$nim = $_POST['nim'] ?? null;
$message = '';

if ($nim) {
    try {
        // Hapus mahasiswa dari database
        $deleteStmt = $pdo->prepare("DELETE FROM students WHERE nim = ?");
        $deleteStmt->execute([$nim]);
        $message = "Mahasiswa berhasil dihapus.";
    } catch (PDOException $e) {
        $message = "Terjadi kesalahan: " . $e->getMessage();
    }
}

header('Location: index.php'); // Redirect kembali ke halaman utama setelah delete
exit;
?>
    