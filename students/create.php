<?php
require_once '../middleware/isAuthenticated.php';
require '../db.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'] ?? null;
    $name = $_POST['name'] ?? null;
    $password = $_POST['password'] ?? null;

    if ($nim && $name && $password) {
        try {

            $stmt = $pdo->prepare("SELECT * FROM students WHERE nim = ?");
            $stmt->execute([$nim]);
            $student = $stmt->fetch();

            if ($student) {
                $message = "NIM sudah terdaftar.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                $insertStmt = $pdo->prepare("INSERT INTO students (nim, name, password) VALUES (?, ?, ?)");
                $insertStmt->execute([$nim, $name, $hashedPassword]);

                header('Location: index.php');
                exit();
            }
        } catch (PDOException $e) {
            $message = "Terjadi kesalahan: " . $e->getMessage();
        }
    } else {
        $message = "Silakan masukkan NIM, Nama mahasiswa, dan password.";
    }
}
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-4">Tambah Mahasiswa</h1>

        <?php if ($message): ?>
            <div class="<?= strpos($message, 'berhasil') !== false ? 'text-green-500' : 'text-red-500' ?> mb-4 p-3 border rounded">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-white p-6 rounded shadow-md mb-6">
            <label for="nim" class="block text-lg font-medium mb-2">NIM Mahasiswa:</label>
            <input type="text" id="nim" name="nim" class="w-full p-3 border border-gray-300 rounded mb-4" placeholder="NIM Mahasiswa">

            <label for="name" class="block text-lg font-medium mb-2">Nama Mahasiswa:</label>
            <input type="text" id="name" name="name" class="w-full p-3 border border-gray-300 rounded mb-4" placeholder="Nama Mahasiswa">
            <label for="password" class="block text-lg font-medium mb-2">Password:</label>
            <input type="text" id="password" name="password" class="w-full p-3 border border-gray-300 rounded mb-4" placeholder="Password">

            <button type="submit" class="w-full bg-blue-500 text-white p-3 rounded hover:bg-blue-600">Tambah Mahasiswa</button>
        </form>
    </div>
</body>

</html>