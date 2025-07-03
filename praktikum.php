<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: login.php");
    exit;
}

require '../config/db.php';

// Tambah praktikum
if (isset($_POST['tambah'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];

    $stmt = $conn->prepare("INSERT INTO praktikum (nama, deskripsi) VALUES (?, ?)");
    $stmt->bind_param("ss", $nama, $deskripsi);
    $stmt->execute();
    header("Location: praktikum.php");
}

// Hapus praktikum
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: praktikum.php");
}

// Ambil data praktikum
$praktikum = $conn->query("SELECT * FROM praktikum");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Mata Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Kelola Mata Praktikum</h1>
        <a href="dashboard.php" class="text-blue-600 underline mb-4 inline-block">← Kembali ke Dashboard</a>
        
        <form method="POST" class="bg-white p-4 rounded shadow-md mb-6">
            <h2 class="text-lg font-semibold mb-2">Tambah Praktikum Baru</h2>
            <input type="text" name="nama" placeholder="Nama Praktikum" required class="w-full mb-2 px-3 py-2 border rounded">
            <textarea name="deskripsi" placeholder="Deskripsi" class="w-full mb-2 px-3 py-2 border rounded"></textarea>
            <button type="submit" name="tambah" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Tambah</button>
        </form>

        <h2 class="text-lg font-semibold mb-2">Daftar Mata Praktikum</h2>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">Nama</th>
                    <th class="border border-gray-300 px-4 py-2">Deskripsi</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $praktikum->fetch_assoc()): ?>
                <tr>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['deskripsi']) ?></td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="praktikum.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus praktikum ini?')" class="text-red-500 underline">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
