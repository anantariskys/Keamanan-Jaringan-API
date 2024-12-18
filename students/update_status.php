<?php
require '../db.php';

$nim = $_GET['nim'] ?? null;

if (!$nim) {
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT ukt_paid FROM students WHERE nim = ?");
    $stmt->execute([$nim]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Mahasiswa dengan NIM $nim tidak ditemukan.");
    }

    $newStatus = $student['ukt_paid'] ? 0 : 1;

    $updateStmt = $pdo->prepare("UPDATE students SET ukt_paid = ? WHERE nim = ?");
    $updateStmt->execute([$newStatus, $nim]);

    header('Location: index.php');
    exit;
} catch (PDOException $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}
