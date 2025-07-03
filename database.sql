CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('mahasiswa','asisten') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Mata Praktikum
CREATE TABLE praktikum (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    deskripsi TEXT
);

-- Tabel Modul Praktikum
CREATE TABLE modul (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_praktikum INT,
    judul VARCHAR(100),
    file_materi VARCHAR(100),
    FOREIGN KEY (id_praktikum) REFERENCES praktikum(id)
);

-- Tabel Pendaftaran Praktikum
CREATE TABLE pendaftaran (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_praktikum INT,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_praktikum) REFERENCES praktikum(id)
);

-- Tabel Laporan Mahasiswa
CREATE TABLE laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT,
    id_modul INT,
    file_laporan VARCHAR(100),
    nilai INT,
    feedback TEXT,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_modul) REFERENCES modul(id)
);