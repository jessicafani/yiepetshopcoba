<?php
include 'config.php'; // Pastikan ini adalah file koneksi database

// Menangani penambahan produk
$notification = ''; // Variabel untuk menyimpan pesan notifikasi
if (isset($_POST['add_product'])) {
    $merek_produk = $_POST['merek_produk'];
    $deskripsi_produk = $_POST['deskripsi_produk'];
    $harga = $_POST['harga'];
    $stok_produk = $_POST['stok_produk'];
    $kategori = $_POST['kategori'];

    // Menangani upload gambar
    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_folder = 'images/' . $image_name;

    // Insert data ke database
    $insert_query = "INSERT INTO yie_product (merek_produk, deskripsi_produk, harga, stok_produk, kategori, image)
                     VALUES ('$merek_produk', '$deskripsi_produk', '$harga', '$stok_produk', '$kategori', '$image_name')";

    if (mysqli_query($conn, $insert_query)) {
        // Jika upload gambar berhasil
        if (move_uploaded_file($image_tmp, $image_folder)) {
            $notification = 'Produk berhasil ditambahkan!'; // Mengatur pesan notifikasi
        } else {
            $notification = 'Gagal mengunggah gambar.'; // Perbaiki untuk menggunakan notifikasi
        }
    } else {
        $notification = 'Gagal menambahkan produk.'; // Perbaiki untuk menggunakan notifikasi
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Y.I.E Petshop</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&family=Raleway:wght@400;600&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            padding: 20px;
            max-width: 800px;
            margin: 30px auto;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            position: relative; /* Tambahkan posisi relatif untuk posisi tombol */
        }
        h1 {
            text-align: center;
            color: #ec407a;
            margin-bottom: 20px;
            font-size: 28px;
        }
        .edit-button {
            position: absolute;
            top: 20px; /* Jarak dari atas */
            right: 20px; /* Jarak dari kanan */
            padding: 10px 20px;
            background-color: #ec407a; /* Warna tombol */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s;
        }
        .edit-button:hover {
            background-color: #f48fb1; /* Warna saat hover */
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input[type="text"],
        input[type="number"],
        textarea,
        input[type="file"],
        select {
            padding: 12px;
            border: 2px solid #ec407a;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input[type="text"]:focus,
        input[type="number"]:focus,
        textarea:focus,
        select:focus {
            border-color: #f48fb1;
            box-shadow: 0 0 8px rgba(236, 64, 122, 0.3);
            outline: none;
        }
        button {
            padding: 12px;
            background-color: #ec407a;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        button:hover {
            background-color: #f48fb1;
            transform: translateY(-2px);
        }
        button:active {
            transform: translateY(1px);
        }
        .notification {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f48fb1;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
            z-index: 1000;
            display: none;
        }
        .notification.show {
            display: block;
        }
        .notification button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: white;
            color: #ec407a;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }
        .notification button:hover {
            background-color: #ec407a;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Y.I.E Petshop</div>
    </header>
    <nav>
        <a href="index.php" class="nav-link">Home Page</a>
        <a href="products.php" class="nav-link">All Y.I.E's Products</a>
        <a href="contact.php" class="nav-link">Contact</a>
    </nav>

    <div class="container">
        <h1>Tambah Produk</h1>
        <button class="edit-button" onclick="window.location.href='edit.php'">Edit Produk</button> <!-- Tombol Edit -->
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="merek_produk" placeholder="Merek Produk" required>
            <textarea name="deskripsi_produk" placeholder="Deskripsi Produk" required></textarea>
            <input type="number" name="harga" placeholder="Harga" required>
            <input type="number" name="stok_produk" placeholder="Stok Produk" required>
            <select name="kategori" required>
                <option value="">Pilih Kategori</option>
                <option value="Dog Food">Dog Food</option>
                <option value="Cat Food">Cat Food</option>
                <option value="Accessories">Accessories</option>
                <option value="Grooming">Grooming</option>
            </select>
            <input type="file" name="image" required>
            <button type="submit" name="add_product">Tambah Produk</button>
        </form>
    </div>

    <!-- Notifikasi Modal -->
    <?php if ($notification): ?>
        <div class="notification show" id="notification">
            <p><?php echo $notification; ?></p>
            <button onclick="closeNotification()">OK</button>
        </div>
        <script>
            // Fungsi untuk menutup notifikasi
            function closeNotification() {
                const notification = document.getElementById('notification');
                notification.classList.remove('show');
            }
        </script>
    <?php endif; ?>

</body>
</html>
