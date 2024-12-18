<?php
require_once '../middleware/isAuthenticated.php';

require '../db.php';

try {
    // Ambil semua data mahasiswa
    $stmt = $pdo->query("SELECT * FROM students ORDER BY nim");
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data mahasiswa: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran UKT</title>
    <!-- Link ke CDN Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="container mx-auto p-6">
        <h1 class="text-3xl font-bold text-center text-blue-600 mb-6">Status Pembayaran UKT Mahasiswa</h1>
        <div class="flex gap-2">
            <!-- Tombol Create -->
            <div class="mb-6 text-center">
                <a href="create.php" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600">Tambah Mahasiswa</a>
            </div>
            <div class="mb-6 text-center">
                <a href="../logout.php" class="bg-blue-500 text-white py-2 px-6 rounded hover:bg-blue-600">Logout</a>
            </div>

        </div>

        <!-- Tabel Data Mahasiswa -->
        <div class="overflow-x-auto bg-white shadow-lg rounded-lg">
            <table class="min-w-full table-auto border-collapse">
                <thead class="bg-blue-500 text-white">
                    <tr>
                        <th class="px-4 py-2 text-left">NIM</th>
                        <th class="px-4 py-2 text-left">Nama</th>
                        <th class="px-4 py-2 text-left">Status UKT</th>
                        <th class="px-4 py-2 text-left">Keterangan</th>
                        <th class="px-4 py-2 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($students)): ?>
                        <tr>
                            <td colspan="5" class="px-4 py-2 text-center">Tidak ada mahasiswa</td>
                        </tr>
                    <?php endif; ?>
                    <?php foreach ($students as $student): ?>
                        <tr class="border-b hover:bg-gray-100">
                            <td class="px-4 py-2"><?= htmlspecialchars($student['nim']) ?></td>
                            <td class="px-4 py-2"><?= htmlspecialchars($student['name']) ?></td>
                            <td class="px-4 py-2 <?= $student['ukt_paid'] ? 'text-green-500' : 'text-red-500' ?>">
                                <?= $student['ukt_paid'] ? 'Sudah Dibayar' : 'Belum Dibayar' ?>
                            </td>
                            <td class="px-4 py-2 <?= $student['ukt_paid'] ? 'text-green-500' : 'text-red-500' ?>">
                                <?= $student['ukt_paid'] ? 'Boleh ikut kuliah' : 'Tidak boleh ikut kuliah' ?>
                            </td>
                            <td class="px-4 py-2 space-x-2">
                                <a href="update.php?nim=<?= htmlspecialchars($student['nim']) ?>" class="bg-yellow-500 text-white py-1 px-4 rounded hover:bg-yellow-600">Update</a>
                                <a href="update_status.php?nim=<?= htmlspecialchars($student['nim']) ?>" class="bg-green-500 text-white py-1 px-4 rounded hover:bg-green-600">
                                    <?= $student['ukt_paid'] ? 'Set Belum Dibayar' : 'Set Sudah Dibayar' ?>
                                </a>
                                <a href="../reports/index.php?nim=<?= htmlspecialchars($student['nim']) ?>" class="bg-blue-500 text-white py-1 px-4 rounded hover:bg-blue-600">Lihat Report</a>
                                <form action="delete.php" method="POST" class="inline">
                                    <input type="hidden" name="nim" value="<?= htmlspecialchars($student['nim']) ?>">
                                    <button type="submit" class="bg-red-500 text-white py-1 px-4 rounded hover:bg-red-600" onclick="return confirm('Yakin ingin menghapus data mahasiswa?')">Delete</button>
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