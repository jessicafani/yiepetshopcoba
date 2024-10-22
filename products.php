<?php
include 'config.php'; // Pastikan ini adalah file koneksi database

// Ambil data produk dari tabel yie_product dengan pencarian
$search = isset($_POST['search']) ? $_POST['search'] : '';
$category = isset($_POST['category']) ? $_POST['category'] : '';

// Hapus produk jika ada permintaan hapus
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_query = $conn->prepare("DELETE FROM yie_product WHERE id = ?");
    $delete_query->bind_param("i", $delete_id);
    
    if ($delete_query->execute()) {
        $_SESSION['message'] = 'Produk berhasil dihapus.';
    } else {
        $_SESSION['message'] = 'Gagal menghapus produk.';
    }
    $delete_query->close();
    header('Location: products.php'); // Redirect setelah penghapusan
    exit;
}

// Mengatur query SQL berdasarkan pencarian dan kategori
$sql = "SELECT * FROM yie_product WHERE merek_produk LIKE '%$search%'";

// Filter berdasarkan kategori yang dipilih
if ($category) {
    if ($category == 'dog_food') {
        $sql .= " AND deskripsi_produk LIKE '%makanan anjing%'";
    } elseif ($category == 'cat_food') {
        $sql .= " AND deskripsi_produk LIKE '%makanan kucing%'";
    }
}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Y.I.E Petshop Product</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&family=Raleway:wght@400;600&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .product img {
            width: 50%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
        }
        .stock {
            font-size: 16px;
            color: green;
            margin-top: 5px;
        }
        .out-of-stock {
            color: red;
        }
        .search-container {
            margin: 20px 0;
            text-align: center;
        }
        .search-container input[type="text"], .search-container select {
            padding: 10px;
            border: 2px solid #ec407a;
            border-radius: 5px;
            font-size: 16px;
        }
        .search-container select {
            margin-right: 10px;
        }
        .search-container button {
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            background-color: #ec407a;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .search-container button:hover {
            background-color: #f48fb1;
        }
        /* Styling untuk tombol tambah produk */
        .add-product-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 15px 20px;
            background-color: #ec407a;
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.2);
            transition: background-color 0.3s;
        }
        .add-product-btn:hover {
            background-color: #f48fb1;
        }
    </style>
    <script>
        // Mengirimkan form saat kategori berubah
        function submitForm() {
            document.getElementById('searchForm').submit();
        }
    </script>
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
        <h1><center>All Y.I.E's Products</center></h1>
        <div class="search-container">
            <form id="searchForm" method="POST" action="">
                <select name="category" onchange="submitForm()">
                    <option value="">All Product</option>
                    <option value="dog_food" <?php if ($category == 'dog_food') echo 'selected'; ?>>Dog Food</option>
                    <option value="cat_food" <?php if ($category == 'cat_food') echo 'selected'; ?>>Cat Food</option>
                </select>
                <input type="text" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search); ?>">
                <button type="submit">Cari</button>
            </form>
        </div>
        <div class="product-grid">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="product">
                        <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['merek_produk']); ?>">
                        <h2><?php echo htmlspecialchars($row['merek_produk']); ?></h2>
                        <p><?php echo htmlspecialchars($row['deskripsi_produk']); ?></p>
                        <p class="price">Rp. <?php echo number_format($row['harga'], 2, ',', '.'); ?></p>
                        <p class="stock">
                            Stok tersedia: <?php echo htmlspecialchars($row['stok_produk']); ?>
                            <?php if ($row['stok_produk'] == 0): ?>
                                <span class="out-of-stock">Stok habis</span>
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Tidak ada produk tersedia.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Button Tambah Produk -->
    <a href="tambah_product.php">
        <button class="add-product-btn">Tambah Produk</button>
    </a>

    <footer>
        <p>&copy; 2024 Y.I.E Petshop</p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
