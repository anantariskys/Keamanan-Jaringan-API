<?php
require '../db.php';

// Ambil NIM dari parameter URL atau sesi
if (isset($_GET['nim'])) {
    $nim = $_GET['nim'];
} else {
    die("NIM tidak diberikan.");
}

// Proses form jika metode adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nim = $_POST['nim'];
    $nilai = $_POST['ipk'];
    $semester = $_POST['semester'];

    try {
        $stmt = $pdo->prepare("INSERT INTO reports (student_nim, ipk, semester) VALUES (?, ?, ?)");
        $stmt->execute([$nim, $nilai, $semester]);
        header('Location: index.php?nim=' . $nim);
        exit;
    } catch (PDOException $e) {
        die("Gagal menambahkan data: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Tambah Laporan</h1>
        <form action="" method="POST" class="bg-white p-4 shadow-md rounded">
            <!-- Input NIM yang sudah diisi otomatis -->
            <div class="mb-4">
                <label class="block font-medium">NIM</label>
                <input type="text" name="nim" value="<?= htmlspecialchars($nim) ?>" class="w-full p-2 border rounded bg-gray-200" readonly>
            </div>
            <div class="mb-4">
                <label class="block font-medium">IPK</label>
                <input type="number" step="0.01" name="ipk" class="w-full p-2 border rounded" required>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Semester</label>
                <select name="semester" class="w-full p-2 border rounded" required>
                    <option value="" disabled selected>Pilih Semester</option>
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <option value="<?= $i ?>">Semester <?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Simpan</button>
        </form>
    </div>
</body>

</html>