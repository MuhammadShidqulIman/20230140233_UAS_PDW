<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mahasiswa') {
    header("Location: login.php");
    exit;
}

require '../config/db.php';
$user_id = $_SESSION['user_id'];

// Ambil daftar praktikum yang diikuti mahasiswa
$query = $conn->prepare("
    SELECT p.id, p.nama, p.deskripsi 
    FROM praktikum p
    JOIN pendaftaran d ON p.id = d.id_praktikum
    WHERE d.id_user = ?
");
$query->bind_param("i", $user_id);
$query->execute();
$praktikum_result = $query->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Praktikum Saya</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Praktikum Saya</h1>
        <a href="dashboard.php" class="text-blue-600 underline mb-4 inline-block">← Kembali ke Dashboard</a>

        <?php while ($praktikum = $praktikum_result->fetch_assoc()): ?>
            <div class="bg-white p-4 rounded shadow-md mb-6">
                <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($praktikum['nama']) ?></h2>
                <p class="mb-2"><?= htmlspecialchars($praktikum['deskripsi']) ?></p>

                <h3 class="font-bold mb-2">Modul & Laporan:</h3>
                <?php
                $modul_query = $conn->prepare("SELECT * FROM modul WHERE id_praktikum = ?");
                $modul_query->bind_param("i", $praktikum['id']);
                $modul_query->execute();
                $modul_result = $modul_query->get_result();
                ?>

                <?php while ($modul = $modul_result->fetch_assoc()): ?>
                    <div class="border-t pt-2 mt-2">
                        <h4 class="font-semibold"><?= htmlspecialchars($modul['judul']) ?></h4>
                        <a href="../uploads/materi/<?= $modul['file_materi'] ?>" class="text-blue-600 underline" download>Unduh Materi</a>

                        <!-- Form Upload Laporan -->
                        <form method="POST" enctype="multipart/form-data" class="mt-2">
                            <input type="file" name="laporan" required class="mb-2">
                            <input type="hidden" name="id_modul" value="<?= $modul['id'] ?>">
                            <button type="submit" name="upload_<?= $modul['id'] ?>" class="bg-green-500 text-white px-2 py-1 rounded hover:bg-green-600">Upload Laporan</button>
                        </form>

                        <?php
                        // Proses upload laporan
                        $upload_key = 'upload_' . $modul['id'];
                        if (isset($_POST[$upload_key]) && $_POST['id_modul'] == $modul['id']) {
                            $file_name = basename($_FILES['laporan']['name']);
                            $target_dir = "../uploads/laporan/";
                            $target_file = $target_dir . $file_name;
                            move_uploaded_file($_FILES['laporan']['tmp_name'], $target_file);

                            // Cek apakah sudah ada laporan sebelumnya
                            $cek = $conn->prepare("SELECT id FROM laporan WHERE id_user = ? AND id_modul = ?");
                            $cek->bind_param("ii", $user_id, $modul['id']);
                            $cek->execute();
                            $cek_result = $cek->get_result();

                            if ($cek_result->num_rows > 0) {
                                $stmt = $conn->prepare("UPDATE laporan SET file_laporan = ? WHERE id_user = ? AND id_modul = ?");
                                $stmt->bind_param("sii", $file_name, $user_id, $modul['id']);
                            } else {
                                $stmt = $conn->prepare("INSERT INTO laporan (id_user, id_modul, file_laporan) VALUES (?, ?, ?)");
                                $stmt->bind_param("iis", $user_id, $modul['id'], $file_name);
                            }
                            $stmt->execute();
                            echo "<p class='text-green-600'>Laporan berhasil diupload.</p>";
                        }

                        // Tampilkan laporan yang telah diupload
                        $laporan = $conn->prepare("SELECT * FROM laporan WHERE id_user = ? AND id_modul = ?");
                        $laporan->bind_param("ii", $user_id, $modul['id']);
                        $laporan->execute();
                        $laporan_result = $laporan->get_result()->fetch_assoc();

                        if ($laporan_result):
                        ?>
                            <p class="mt-1">Laporan: <a href="../uploads/laporan/<?= $laporan_result['file_laporan'] ?>" class="underline text-blue-600" download><?= htmlspecialchars($laporan_result['file_laporan']) ?></a></p>
                            <p>Nilai: <?= $laporan_result['nilai'] !== null ? $laporan_result['nilai'] : 'Belum dinilai' ?></p>
                            <p>Feedback: <?= $laporan_result['feedback'] ?? '-' ?></p>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
