<?php
session_start(); // Memastikan session aktif
include 'config.php'; // Pastikan ini adalah file koneksi database

// Jika ID produk tidak diberikan, redirect ke halaman produk
if (!isset($_GET['id'])) {
    header('Location: products.php');
    exit;
}

$id = intval($_GET['id']);

// Ambil data produk berdasarkan ID
$product_query = $conn->prepare("SELECT * FROM yie_product WHERE id = ?");
$product_query->bind_param("i", $id);
$product_query->execute();
$product_result = $product_query->get_result();

if ($product_result->num_rows == 0) {
    $_SESSION['message'] = 'Produk tidak ditemukan.';
    header('Location: products.php');
    exit;
}

$product = $product_result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $merek_produk = $_POST['merek_produk'];
    $deskripsi_produk = $_POST['deskripsi_produk'];
    $harga = $_POST['harga'];
    $kategori = $_POST['kategori'];
    $image = $_FILES['image'];

    // Upload image jika ada
    if ($image['error'] == 0) {
        $target_dir = "images/";
        $target_file = $target_dir . basename($image["name"]);
        move_uploaded_file($image["tmp_name"], $target_file);
    } else {
        $target_file = $product['image']; // Gunakan gambar lama jika tidak ada yang baru
    }

    // Update produk
    $update_query = $conn->prepare("UPDATE yie_product SET merek_produk = ?, deskripsi_produk = ?, harga = ?, kategori = ?, image = ? WHERE id = ?");
    $update_query->bind_param("sssssi", $merek_produk, $deskripsi_produk, $harga, $kategori, $target_file, $id);

    if ($update_query->execute()) {
        $_SESSION['message'] = 'Produk berhasil diperbarui.';
        header('Location: products.php');
        exit;
    } else {
        $_SESSION['message'] = 'Gagal memperbarui produk.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Y.I.E Petshop</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&family=Raleway:wght@400;600&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Quicksand', sans-serif;
            background-color: #f1f1f1;
            color: #333;
        }

        header {
            background-color: #ec407a;
            padding: 20px;
            text-align: center;
            color: white;
            font-size: 24px;
            position: relative;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #ec407a;
        }

        .notification {
            text-align: center;
            margin-bottom: 20px;
            color: #f44336;
        }

        .form-container {
            margin: 0 auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ec407a;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }

        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            border-color: #d81b60; /* Warna border saat fokus */
            outline: none; /* Menghilangkan outline default */
        }

        .form-group button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #f48fb1;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .form-group button:hover {
            background-color: #f48fb1;
            transform: scale(1.05);
        }

        .form-group small {
            display: block;
            margin-top: 5px;
            color: #666;y
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">Y.I.E Petshop</div>
    </header>
    <div class="container">
        <h1>Edit Produk</h1>
        <div class="notification">
            <?php
            // Tampilkan notifikasi jika ada
            if (isset($_SESSION['message'])) {
                echo $_SESSION['message'];
                unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
            }
            ?>
        </div>
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="merek_produk">Nama Produk</label>
                    <input type="text" name="merek_produk" id="merek_produk" value="<?php echo htmlspecialchars($product['merek_produk']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="deskripsi_produk">Deskripsi Produk</label>
                    <textarea name="deskripsi_produk" id="deskripsi_produk" rows="4" required><?php echo htmlspecialchars($product['deskripsi_produk']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="number" name="harga" id="harga" value="<?php echo htmlspecialchars($product['harga']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select name="kategori" id="kategori" required>
                        <option value="Dog Food" <?php if ($product['kategori'] == 'Dog Food') echo 'selected'; ?>>Dog Food</option>
                        <option value="Cat Food" <?php if ($product['kategori'] == 'Cat Food') echo 'selected'; ?>>Cat Food</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="image">Gambar Produk</label>
                    <input type="file" name="image" id="image">
                    <small>Biarkan kosong jika tidak ingin mengubah gambar.</small>
                </div>
                <div class="form-group">
                    <button type="submit">Perbarui Produk</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
