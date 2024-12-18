<?php
require '../db.php';

$id = $_GET['id'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $nilai = $_POST['nilai'];
    $semester = $_POST['semester'];

    try {
        $stmt = $pdo->prepare("UPDATE reports SET student_nim = ?, ipk = ?, semester = ? WHERE id = ?");
        $stmt->execute([$user_id, $nilai, $semester, $id]);
        header('Location: index.php?nim=' . $user_id);
        exit;
    } catch (PDOException $e) {
        die("Gagal memperbarui data: " . $e->getMessage());
    }
}

try {
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE id = ?");
    $stmt->execute([$id]);
    $report = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Gagal mengambil data: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="container mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Edit Laporan</h1>
        <form action="" method="POST" class="bg-white p-4 shadow-md rounded">
            <div class="mb-4">
                <label class="block font-medium">User ID</label>
                <input type="number" name="user_id" value="<?= htmlspecialchars($report['student_nim']) ?>" class="w-full p-2 border bg-gray-200     rounded" readonly>
            </div>
            <div class="mb-4">
                <label class="block font-medium">Nilai</label>
                <input type="number" step="0.01" name="nilai" value="<?= htmlspecialchars($report['ipk']) ?>" class="w-full p-2 border rounded" required>
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

            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Update</button>
        </form>
    </div>
</body>

</html>