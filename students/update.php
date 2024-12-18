<?php
require_once '../middleware/isAuthenticated.php';

require '../db.php';


$nim = $_GET['nim'] ?? null;

if (!$nim) {
    header('Location: index.php');
    exit;
}

$message = '';


try {
    $stmt = $pdo->prepare("SELECT * FROM students WHERE nim = ?");
    $stmt->execute([$nim]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        die("Mahasiswa dengan NIM $nim tidak ditemukan.");
    }
} catch (PDOException $e) {
    die("Terjadi kesalahan: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';

    if (!empty($name)) {
        try {

            $updateStmt = $pdo->prepare("UPDATE students SET name = ? WHERE nim = ?");
            $updateStmt->execute([$name, $nim]);

            $message = "Nama mahasiswa berhasil diperbarui.";
        } catch (PDOException $e) {
            $message = "Terjadi kesalahan saat memperbarui data: " . $e->getMessage();
        }
    } else {
        $message = "Nama tidak boleh kosong.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Nama Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold text-blue-600 mb-6">Update Nama Mahasiswa</h1>

        <?php if ($message): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" class="bg-white shadow-lg rounded-lg p-6">
            <div class="mb-4">
                <label for="nim" class="block text-gray-700 font-bold mb-2">NIM</label>
                <input type="text" id="nim" name="nim" value="<?= htmlspecialchars($student['nim']) ?>" class="border border-gray-300 rounded px-4 py-2 w-full" readonly>
            </div>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Mahasiswa</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($student['name']) ?>" class="border border-gray-300 rounded px-4 py-2 w-full" required>
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-blue-500 text-white py-2 px-4 rounded hover:bg-blue-600">Update Nama</button>
                <a href="index.php" class="text-blue-500 hover:underline">Kembali</a>
            </div>
        </form>
    </div>
</body>

</html>