<?php
require '../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $nim = $_POST['nim'];

    try {
        $stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
        $stmt->execute([$id]);
        header('Location: index.php?nim=' . $nim);
        exit;
    } catch (PDOException $e) {
        die("Gagal menghapus data: " . $e->getMessage());
    }
}
?>
