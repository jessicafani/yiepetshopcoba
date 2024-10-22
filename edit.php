<?php
session_start(); // Memastikan session aktif
include 'config.php'; // Pastikan ini adalah file koneksi database

// Definisikan variabel dengan nilai default
$search = ''; 
$category = '';

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

// Ambil data dari form jika ada
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search = isset($_POST['search']) ? $_POST['search'] : '';
    $category = isset($_POST['category']) ? $_POST['category'] : '';
}

// Mengatur query SQL berdasarkan pencarian dan kategori
$sql = "SELECT * FROM yie_product WHERE merek_produk LIKE '%$search%'";

// Filter berdasarkan kategori yang dipilih
if ($category) {
    if ($category == 'dog_food') {
        $sql .= " AND kategori = 'Dog Food'";
    } elseif ($category == 'cat_food') {
        $sql .= " AND kategori = 'Cat Food'";
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
        /* CSS styling untuk elemen */
        .product img {
            width: 50%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
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

        .product {
            position: relative;
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }

        .product-buttons {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .product-buttons a {
            padding: 8px 12px;
            border-radius: 5px;
            color: white;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .edit-button {
            background-color: #2196F3;
        }

        .edit-button:hover {
            background-color: #1976D2;
        }

        .delete-button {
            background-color: #f44336;
        }

        .delete-button:hover {
            background-color: #d32f2f;
        }

        .back-button {
            position: absolute;
            top: 20px;
            left: 20px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #f8bbd0;
            color: #ec407a;
            font-size: 20px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .back-button:hover {
            background-color: #f48fb1;
        }

        /* CSS untuk notifikasi bubble */
        .notification-bubble {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ec407a;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            text-align: center;
            width: 300px;
        }

        .notification-bubble p {
            margin: 0 0 15px;
            font-size: 16px;
        }

        .notification-bubble button {
            padding: 10px 15px;
            margin: 5px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .notification-bubble .yes-button {
            background-color: #f44336;
            color: white;
        }

        .notification-bubble .no-button {
            background-color: #2196F3;
            color: white;
        }

        .notification-bubble button:hover {
            opacity: 0.9;
        }
    </style>
    <script>
        let deleteId;

        // Konfirmasi sebelum menghapus produk
        function confirmDelete(id) {
            deleteId = id; // Simpan id produk yang ingin dihapus
            document.getElementById('notificationBubble').style.display = 'block'; // Tampilkan notifikasi
        }

        function cancelDelete() {
            document.getElementById('notificationBubble').style.display = 'none'; // Sembunyikan notifikasi
        }

        function deleteProduct() {
            window.location.href = 'products.php?delete_id=' + deleteId; // Redirect untuk menghapus produk
        }
    </script>
</head>
<body>
    <header>
        <div class="logo">Y.I.E Petshop</div>
        <button class="back-button" onclick="window.location.href='tambah_product.php'">&lt; Kembali</button>
    </header>
    <div class="notification">
        <?php
        // Tampilkan notifikasi jika ada
        if (isset($_SESSION['message'])) {
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Hapus pesan setelah ditampilkan
        }
        ?>
    </div>

    <!-- Notifikasi Bubble -->
    <div class="notification-bubble" id="notificationBubble">
        <p>Apakah Anda yakin ingin menghapus produk ini?</p>
        <button class="yes-button" onclick="deleteProduct()">Ya</button>
        <button class="no-button" onclick="cancelDelete()">Tidak</button>
    </div>

    <div class="container">   
        <?php if (!isset($_GET['delete_id']) || $result->num_rows > 0): ?>
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
                            <div class="product-buttons">
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="edit-button">Edit</a>
                                <a href="javascript:void(0);" class="delete-button" onclick="confirmDelete(<?php echo $row['id']; ?>)">Hapus</a>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p>Tidak ada produk ditemukan.</p>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p>Produk tidak dapat dihapus.</p>
        <?php endif; ?>
    </div>
</body>
</html>
