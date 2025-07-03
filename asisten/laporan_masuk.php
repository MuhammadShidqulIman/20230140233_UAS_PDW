<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: login.php");
    exit;
}

require '../config/db.php';

// Update nilai & feedback
if (isset($_POST['nilai'])) {
    $id_laporan = $_POST['id_laporan'];
    $nilai = $_POST['nilai'];
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("UPDATE laporan SET nilai = ?, feedback = ? WHERE id = ?");
    $stmt->bind_param("isi", $nilai, $feedback, $id_laporan);
    $stmt->execute();
    header("Location: laporan_masuk.php");
    exit;
}

// Ambil laporan masuk
$laporan = $conn->query("
    SELECT l.*, u.nama, m.judul AS modul_judul, p.nama AS praktikum_nama
    FROM laporan l
    JOIN users u ON l.id_user = u.id
    JOIN modul m ON l.id_modul = m.id
    JOIN praktikum p ON m.id_praktikum = p.id
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Masuk</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Laporan Masuk Mahasiswa</h1>
        <a href="dashboard.php" class="text-blue-600 underline mb-4 inline-block">← Kembali ke Dashboard</a>

        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">Mahasiswa</th>
                    <th class="border border-gray-300 px-4 py-2">Praktikum</th>
                    <th class="border border-gray-300 px-4 py-2">Modul</th>
                    <th class="border border-gray-300 px-4 py-2">Laporan</th>
                    <th class="border border-gray-300 px-4 py-2">Nilai</th>
                    <th class="border border-gray-300 px-4 py-2">Feedback</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $laporan->fetch_assoc()): ?>
                <tr>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['praktikum_nama']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['modul_judul']) ?></td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="../uploads/laporan/<?= $row['file_laporan'] ?>" class="text-blue-600 underline" download>Lihat</a>
                    </td>
                    <td class="border border-gray-300 px-4 py-2"><?= $row['nilai'] ?? '-' ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= $row['feedback'] ?? '-' ?></td>
                    <td class="border border-gray-300 px-4 py-2">
                        <form method="POST">
                            <input type="hidden" name="id_laporan" value="<?= $row['id'] ?>">
                            <input type="number" name="nilai" placeholder="Nilai" required class="w-full mb-1 px-2 py-1 border rounded" value="<?= $row['nilai'] ?? '' ?>">
                            <textarea name="feedback" placeholder="Feedback" required class="w-full mb-1 px-2 py-1 border rounded"><?= $row['feedback'] ?? '' ?></textarea>
                            <button type="submit" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600 w-full">Simpan</button>
                        </form>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
