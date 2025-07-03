<?php
session_start();
require '../config/db.php';

$is_logged_in = isset($_SESSION['user_id']) && $_SESSION['role'] === 'mahasiswa';
$user_id = $_SESSION['user_id'] ?? null;

// Daftar ke praktikum
if (isset($_POST['daftar'])) {
    $id_praktikum = $_POST['id_praktikum'];

    // Cek apakah sudah mendaftar sebelumnya
    $cek = $conn->prepare("SELECT * FROM pendaftaran WHERE id_user = ? AND id_praktikum = ?");
    $cek->bind_param("ii", $user_id, $id_praktikum);
    $cek->execute();
    $result = $cek->get_result();

    if ($result->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO pendaftaran (id_user, id_praktikum) VALUES (?, ?)");
        $stmt->bind_param("ii", $user_id, $id_praktikum);
        $stmt->execute();
        $success = "Berhasil mendaftar!";
    } else {
        $error = "Anda sudah terdaftar di praktikum ini.";
    }
}

// Ambil daftar praktikum
$praktikum = $conn->query("SELECT * FROM praktikum");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Katalog Praktikum</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Katalog Mata Praktikum</h1>
        <a href="dashboard.php" class="text-blue-600 underline mb-4 inline-block">← Kembali ke Dashboard</a>

        <?php if (isset($success)) echo "<p class='text-green-600 mb-2'>$success</p>"; ?>
        <?php if (isset($error)) echo "<p class='text-red-600 mb-2'>$error</p>"; ?>

        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">Nama</th>
                    <th class="border border-gray-300 px-4 py-2">Deskripsi</th>
                    <?php if ($is_logged_in): ?>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $praktikum->fetch_assoc()): ?>
                <tr>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['nama']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['deskripsi']) ?></td>
                    <?php if ($is_logged_in): ?>
                    <td class="border border-gray-300 px-4 py-2">
                        <form method="POST">
                            <input type="hidden" name="id_praktikum" value="<?= $row['id'] ?>">
                            <button type="submit" name="daftar" class="bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">Daftar</button>
                        </form>
                    </td>
                    <?php endif; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
