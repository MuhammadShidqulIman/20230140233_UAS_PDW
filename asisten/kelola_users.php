<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'asisten') {
    header("Location: login.php");
    exit;
}

require '../config/db.php';

// Tambah Akun
if (isset($_POST['tambah'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("INSERT INTO users (username, password, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $password, $role);
    $stmt->execute();
    header("Location: kelola_users.php");
}

// Hapus Akun
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: kelola_users.php");
}

// Ambil Data User
$users = $conn->query("SELECT * FROM users");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Kelola Akun Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="p-6">
        <h1 class="text-2xl font-bold mb-4">Kelola Akun Pengguna</h1>
        <a href="dashboard.php" class="text-blue-600 underline mb-4 inline-block">← Kembali ke Dashboard</a>

        <!-- Form Tambah Akun -->
        <form method="POST" class="bg-white p-4 rounded shadow-md mb-6">
            <h2 class="text-lg font-semibold mb-2">Tambah Akun Baru</h2>
            <input type="text" name="username" placeholder="Username" required class="w-full mb-2 px-3 py-2 border rounded">
            <input type="password" name="password" placeholder="Password" required class="w-full mb-2 px-3 py-2 border rounded">
            <select name="role" required class="w-full mb-2 px-3 py-2 border rounded">
                <option value="">Pilih Peran</option>
                <option value="mahasiswa">Mahasiswa</option>
                <option value="asisten">Asisten</option>
            </select>
            <button type="submit" name="tambah" class="bg-green-500 text-white px-4 py-2 rounded hover:bg-green-600">Tambah</button>
        </form>

        <!-- Daftar Akun -->
        <h2 class="text-lg font-semibold mb-2">Daftar Akun Pengguna</h2>
        <table class="w-full table-auto border-collapse border border-gray-300">
            <thead>
                <tr class="bg-gray-200">
                    <th class="border border-gray-300 px-4 py-2">Username</th>
                    <th class="border border-gray-300 px-4 py-2">Role</th>
                    <th class="border border-gray-300 px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['username']) ?></td>
                    <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($row['role']) ?></td>
                    <td class="border border-gray-300 px-4 py-2">
                        <a href="kelola_users.php?hapus=<?= $row['id'] ?>" onclick="return confirm('Hapus akun ini?')" class="text-red-500 underline">Hapus</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
