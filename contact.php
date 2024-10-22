<?php
// Konfigurasi koneksi ke database
$servername = "localhost";
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda
$dbname = "db_yie";

// Membuat koneksi ke database
$conn = new mysqli($servername, $username, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Variabel untuk menyimpan pesan status
$messageStatus = "";

// Mengecek apakah form sudah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mengambil data dari form
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Menyimpan data ke tabel db_contact
    $sql = "INSERT INTO db_contact (name, email, message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    // Memeriksa apakah statement berhasil dipersiapkan
    if ($stmt) {
        // Mengikat parameter
        $stmt->bind_param("sss", $name, $email, $message);
        
        // Menjalankan statement
        if ($stmt->execute()) {
            $messageStatus = "Pesan berhasil dikirim! Terima kasih telah menghubungi kami.";
        } else {
            $messageStatus = "Terjadi kesalahan saat mengirim pesan: " . $stmt->error;
        }
        
        // Menutup statement
        $stmt->close();
    } else {
        $messageStatus = "Terjadi kesalahan: " . $conn->error;
    }
}

// Mengambil data komentar dari tabel db_contact
$comments_sql = "SELECT name, message FROM db_contact ORDER BY id DESC";
$comments_result = $conn->query($comments_sql);

// Menutup koneksi ke database
$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Y.I.E Petshop</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;700&family=Raleway:wght@400;600&family=Pacifico&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Pacifico', sans-serif;
            background-color: #fce4ec;
            color: #ec407a;
            font-size: 20px;
        }
        .contact-links {
            margin-top: 20px;
            text-align: center;
            padding: 20px;
        }

        .contact-links a {
            margin: 0 15px;
            text-decoration: none;
            color: #ec407a;
            font-weight: bold;
            font-size: 20px;
            display: inline-block;
            padding: 10px 15px;
            border-radius: 50px;
            background-color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .contact-links a:hover {
            color: white;
            background-color: #d81b60;
        }

        .contact-links a i {
            margin-right: 8px;
        }


        .modal {
            display: none; /* Hidden secara default */
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .modal-content button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #f48fb1;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .modal-content button:hover {
            background-color: #ec407a;
        }

        .comments-section {
            margin-top: 30px;
        }

        .comments-section h3 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #ec407a;
        }

        .comment {
            border-bottom: 1px solid #ddd;
            padding: 10px 0;
        }

        .comment .username {
            font-weight: bold;
            font-size: 18px;
            color: #333;
        }

        .comment .message {
            font-size: 16px;
            color: #555;
        }

        .container {
            padding: 20px;
            background-color: #fff;
            margin: 20px auto;
            max-width: 800px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .contact-form label {
            display: block;
            margin: 10px 0 5px;
        }

        .contact-form input, .contact-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 10px;
        }

        .contact-form button {
            background-color: #f06292;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 50px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .contact-form button:hover {
            background-color: #d81b60;
        }

        .comments-section {
            margin-top: 30px;
        }

        .comments-section h3 {
            font-size: 28px;
            margin-bottom: 20px;
            color: #ec407a;
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

    <div class="contact-links">
        <h3>ORDER HERE</h3>
        <a href="https://shopee.co.id/sonya_herlina" target="_blank">
            <i class="fa fa-shopping-cart"></i>Shopee
        </a>
        <a href="https://www.tokopedia.com/archive-suwignyopetshop" target="_blank">
            <i class="fa fa-shopping-bag"></i>Tokopedia
        </a>
        <a href="https://www.instagram.com/yiepetshop" target="_blank">
            <i class="fa fa-instagram"></i>Instagram
        </a>
        <a href="https://wa.me/6281220785766" target="_blank">
            <i class="fa fa-whatsapp"></i>WhatsApp
        </a>
    </div>
    <div class="container">
        <h2>Send Us a Message</h2>

        <form class="contact-form" action="contact.php" method="post">
            <label for="name">Name</label>
            <input type="text" id="name" name="name" required>
            
            <label for="email">Email</label>
            <input type="email" id="email" name="email" required>
            
            <label for="message">Message</label>
            <textarea id="message" name="message" rows="5" required></textarea>
            
            <button type="submit">Send Message</button>
        </form>

        <!-- Menampilkan komentar -->
        <div class="comments-section">
            <h3>Komentar Pengguna</h3>
            <?php if ($comments_result->num_rows > 0): ?>
                <?php while($row = $comments_result->fetch_assoc()): ?>
                    <div class="comment">
                        <p class="username"><?php echo htmlspecialchars($row['name']); ?></p>
                        <p class="message"><?php echo htmlspecialchars($row['message']); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Belum ada komentar.</p>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>&copy; 2024 Y.I.E Petshop</p>
    </footer>

    <!-- Modal Notifikasi -->
    <div id="notificationModal" class="modal">
        <div class="modal-content">
            <p id="notificationMessage"></p>
            <button onclick="closeModal()">OK</button>
        </div>
    </div>

    <script>
        // Fungsi untuk menutup modal
        function closeModal() {
            document.getElementById('notificationModal').style.display = 'none';
        }

        // Fungsi untuk membuka modal dengan pesan
        function openModal(message) {
            document.getElementById('notificationMessage').innerText = message;
            document.getElementById('notificationModal').style.display = 'flex';
        }

        // Cek apakah ada pesan status dari PHP
        <?php if ($messageStatus): ?>
            openModal("<?php echo addslashes($messageStatus); ?>");
        <?php endif; ?>
    </script>
</body>
</html>
