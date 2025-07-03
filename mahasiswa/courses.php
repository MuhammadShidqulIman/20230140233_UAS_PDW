<?php
require '../config/db.php';

$search = '';
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $conn->prepare("
        SELECT p.id, p.nama AS praktikum, COUNT(m.id) AS jumlah_modul
        FROM praktikum p
        LEFT JOIN modul m ON p.id = m.id_praktikum
        WHERE p.nama LIKE ?
        GROUP BY p.id, p.nama
    ");
    $param = "%" . $search . "%";
    $stmt->bind_param("s", $param);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $result = $conn->query("
        SELECT p.id, p.nama AS praktikum, COUNT(m.id) AS jumlah_modul
        FROM praktikum p
        LEFT JOIN modul m ON p.id = m.id_praktikum
        GROUP BY p.id, p.nama
    ");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Katalog Mata Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Katalog Mata Praktikum</h1>
        <a href='../mahasiswa/dashboard.php' class="text-blue-600 underline mb-4 inline-block">← Kembali ke Beranda</a>

        <!-- Form pencarian -->
        <form method="GET" class="mb-4">
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari praktikum..." class="border px-4 py-2 rounded w-64">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Cari</button>
        </form>

        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border px-4 py-2">No</th>
                    <th class="border px-4 py-2">Nama Praktikum</th>
                    <th class="border px-4 py-2">Jumlah Modul</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($row = $result->fetch_assoc()):
                ?>
                <tr>
                    <td class="border px-4 py-2"><?= $no++ ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($row['praktikum']) ?></td>
                    <td class="border px-4 py-2"><?= $row['jumlah_modul'] ?></td>
                </tr>
                <?php endwhile; ?>
                <?php if ($result->num_rows === 0): ?>
                <tr>
                    <td colspan="3" class="text-center border px-4 py-2 text-gray-500">Tidak ditemukan.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
