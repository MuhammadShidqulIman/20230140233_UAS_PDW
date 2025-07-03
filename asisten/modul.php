<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: login.php");
    exit;
}

require '../config/db.php';

// Tambah atau Edit Modul
if (isset($_POST['simpan'])) {
    $id = $_POST['id'] ?? '';
    $id_praktikum = $_POST['id_praktikum'];
    $judul = $_POST['judul'];
    $file_name = $_FILES['materi']['name'] ? basename($_FILES['materi']['name']) : $_POST['file_lama'];
    $target_dir = "../uploads/materi/";
    $target_file = $target_dir . $file_name;

    if ($_FILES['materi']['name']) {
        move_uploaded_file($_FILES['materi']['tmp_name'], $target_file);
    }

    if ($id) {
        $stmt = $conn->prepare("UPDATE modul SET id_praktikum = ?, judul = ?, file_materi = ? WHERE id = ?");
        $stmt->bind_param("issi", $id_praktikum, $judul, $file_name, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO modul (id_praktikum, judul, file_materi) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $id_praktikum, $judul, $file_name);
    }
    $stmt->execute();
    header("Location: modul.php");
}

// Hapus Modul
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM modul WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: modul.php");
}

// Ambil Data
$praktikum = $conn->query("SELECT * FROM praktikum");
$modul = $conn->query("SELECT m.*, p.nama AS praktikum_nama FROM modul m JOIN praktikum p ON m.id_praktikum = p.id");

$edit_data = null;
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM modul WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_data = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Modul Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Kelola Modul Praktikum</h1>
        <a href="dashboard.php" class="text-blue-600 underline mb-4 inline-block">← Kembali ke Dashboard</a>

        <!-- Form Tambah/Edit Modul -->
        <form method="POST" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-md mb-6">
            <h2 class="text-lg font-semibold mb-2">
                <?= $edit_data ? 'Edit Modul' : 'Tambah Modul' ?>
            </h2>
            <input type="hidden" name="id" value="<?= $edit_data['id'] ?? '' ?>">
            <input type="hidden" name="file_lama" value="<?= $edit_data['file_materi'] ?? '' ?>">
            <select name="id_praktikum" required class="w-full mb-2 px-3 py-2 border rounded">
                <option value="">Pilih Praktikum</option>
                <?php while ($row = $praktikum->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>" <?= ($edit_data && $edit_data['id_praktikum'] == $row['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($row['nama']) ?>
                </option>
                <?php endwhile; ?>
                <?php mysqli_data_seek($praktikum, 0); ?>
            </select>
            <input type="text" name="judul" placeholder="Judul Modul" required class="w-full mb-2 px-3 py-2 border rounded" value="<?= $edit_data['judul'] ?? '' ?>">
            <input type="file" name="materi" accept=".pdf,.doc,.docx" class="w-full mb-2 px-3 py-2 border rounded">
            <?php if ($edit_data): ?>
                <p class="text-sm text-gray-500 mb-2">File lama: <?= htmlspecialchars($edit_data['file_materi']) ?></p>
            <?php endif; ?>
            <button type="submit" name="simpan" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">
                <?= $edit_data ? 'Update Modul' : 'Tambah Modul' ?>
            </button>
        </form>

        <!-- Tabel Daftar Modul -->
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead class="bg-gray-200">
                <tr>
                    <th class="border px-4 py-2">Praktikum</th>
                    <th class="border px-4 py-2">Judul Modul</th>
                    <th class="border px-4 py-2">Materi</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $modul->fetch_assoc()): ?>
                <tr>
                    <td class="border px-4 py-2"><?= htmlspecialchars($row['praktikum_nama']) ?></td>
                    <td class="border px-4 py-2"><?= htmlspecialchars($row['judul']) ?></td>
                    <td class="border px-4 py-2">
                        <a href="../uploads/materi/<?= htmlspecialchars($row['file_materi']) ?>" class="text-blue-600 underline" target="_blank">Download</a>
                    </td>
                    <td class="border px-4 py-2">
                        <a href="?edit=<?= $row['id'] ?>" class="text-yellow-600 mr-2">Edit</a>
                        <a href="?hapus=<?= $row['id'] ?>" class="text-red-600" onclick="return confirm('Yakin ingin menghapus modul ini?')">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
