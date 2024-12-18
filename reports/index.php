<?php
require '../db.php';

if (isset($_GET['nim'])) {
    $nim = $_GET['nim'];

    try {
        // Ambil data mahasiswa
        $stmt = $pdo->prepare("SELECT * FROM students WHERE nim = ?");
        $stmt->execute([$nim]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        // Periksa apakah mahasiswa ditemukan
        if (!$student) {
            die("Mahasiswa tidak ditemukan.");
        }

        // Ambil data laporan terkait
        $reportStmt = $pdo->prepare("SELECT * FROM reports WHERE student_nim = ? ORDER BY semester ASC");
        $reportStmt->execute([$nim]);
        $reports = $reportStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Gagal mengambil data laporan: " . $e->getMessage());
    }
} else {
    die("Parameter NIM tidak diberikan.");
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Mahasiswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Laporan Mahasiswa</h1>
        <h2 class="text-xl font-semibold">Nama: <?= htmlspecialchars($student['name']) ?></h2>
        <h2 class="text-xl font-semibold">NIM: <?= htmlspecialchars($student['nim']) ?></h2>
        <div class="flex gap-2">
            <div class="mt-6 text-center">
                <a href="../students/index.php?>" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600">Kembali</a>
            </div>
            <!-- Tombol Tambah Laporan Baru -->
            <div class="mt-6 text-center">
                <a href="create.php?nim=<?= htmlspecialchars($nim) ?>" class="bg-green-500 text-white py-2 px-6 rounded hover:bg-green-600">Tambah Laporan Baru</a>
            </div>

        </div>

        <!-- Tabel Laporan -->
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg mt-6">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">ID Laporan</th>
                        <th class="px-4 py-2 text-left">Tanggal</th>
                        <th class="px-4 py-2 text-left">IPK</th>
                        <th class="px-4 py-2 text-left">Semester</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($reports)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center">Tidak ada laporan untuk mahasiswa ini.</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($reports as $index => $report): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?= $index + 1 ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($report['date']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($report['ipk']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($report['semester']) ?></td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="update.php?id=<?= htmlspecialchars($report['id']) ?>" class="bg-yellow-500 text-white py-1 px-4 rounded hover:bg-yellow-600">Update</a>
                                <form action="delete.php" method="POST" class="inline">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($report['id']) ?>">
                                    <input type="hidden" name="nim" value="<?= htmlspecialchars($nim) ?>">
                                    <button type="submit" class="bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600" onclick="return confirm('Yakin ingin menghapus laporan ini?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>


    </div>
</body>

</html>