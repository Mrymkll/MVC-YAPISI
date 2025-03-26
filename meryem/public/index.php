<?php 
include 'config.php';
session_start(); // Oturum başlatma

// Veritabanı bağlantısı
$servername = "sql107.infinityfree.com";
$username = "if0_37730032";
$password = "FLxhachSVKMHgJ";
$dbname = "if0_37730032_meryem";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NAMES 'utf8'");
} catch(PDOException $e) {
    echo "Bağlantı hatası: " . $e->getMessage();
}

// Sepet işlemleri
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = []; // Sepet yoksa tanımla
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    $_SESSION['cart'][$product_id] = isset($_SESSION['cart'][$product_id]) ? $_SESSION['cart'][$product_id] + 1 : 1; // Ürün ekle
    echo count($_SESSION['cart']); // Sepet sayısını döndür
    exit(); // İşlemi sonlandır
}

// Giriş yapma işlemi
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin']) {
    // Kullanıcı bilgilerini veritabanından al
    $eposta = $_SESSION['eposta']; // Oturumdan e-posta al
    $query = "SELECT adsoyad FROM kullanicilar WHERE eposta = :eposta";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':eposta', $eposta);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $adsoyad = $user['adsoyad']; // Veritabanından ad soyadı al
    } else {
        $adsoyad = ''; // Kullanıcı bulunamazsa boş bırak
    }
} else {
    $adsoyad = ''; // Kullanıcı giriş yapmamışsa boş bırak
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mobilya Mağazası | Her Ev Güzel Mobilyalarla</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .main-header {
            background-color: #ffffff;
            padding: 20px 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo img {
            max-width: 80%; 
            height: auto;
        }

        .search-bar {
            position: relative;
            width: 100%;
        }

        .search-bar input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .search-bar button {
            position: absolute;
            right: 0;
            top: 0;
            padding: 10px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
        }

        .user-actions {
            display: flex;
            flex-direction: column;  /* Dikey hizalama */
            align-items: flex-end;   /* Sağ tarafa hizalama */
            gap: 5px;               /* Aralık */
        }

        .user-actions .action-item {
            text-decoration: none;
            color: #333;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .user-actions .cart-count {
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7em;
            margin-left: 5px;
        }

        .hero-banner {
            background-image: url('resimler/banner.jpg');
            background-size: cover;
            color: white;
            text-align: center;
            padding: 50px 20px;
        }

        .hero-banner h2,
        .hero-banner p {
            color: black;
        }

        .main-menu {
            display: flex;
            justify-content: space-around;
            background-color: #007bff;
            padding: 10px 0;
        }

        .main-menu .menu-item a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .main-menu .menu-item a:hover {
            background-color: #0056b3;
        }

        .featured-products {
            padding: 40px 0;
        }

        .product-card {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            text-align: center;
            transition: transform 0.3s;
        }

        .product-card:hover {
            transform: scale(1.05);
        }

        .contact-info {
            background-color: #007bff;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        .contact-info a {
            color: white;
            text-decoration: none;
        }

        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
        }

        .footer-links h5 {
            margin-bottom: 15px;
        }

        .footer-links ul {
            list-style: none;
            padding: 0;
        }

        .footer-links ul li {
            margin-bottom: 10px;
        }

        .footer-links ul li a {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-3">
                    <div class="logo">
                        <a href="index.php">
                            <img src="resimler/logo.jpg" alt="Mobilya Mağazası Logo">
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="search-bar">
                        <form>
                            <input type="text" placeholder="Ne aramıştınız?" required>
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>
                    </div>
                </div>
                <div class="col-lg-3 text-end">
                    <div class="header-actions">
                        <div class="user-actions">
                            <?php if (!empty($adsoyad)): ?>
                                <div class="welcome-box">Hoşgeldiniz, <?php echo htmlspecialchars($adsoyad); ?></div>
                            <?php endif; ?>
                            <a href="login.php" class="action-item">
                                <i class="fas fa-user"></i> Üye Girişi
                            </a>
                            <a href="cart.php" class="action-item">
                                <i class="fas fa-shopping-cart"></i> Sepetim 
                                <span class="cart-count"><?php echo count($_SESSION['cart']); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Banner Section -->
    <div class="hero-banner">
        <div class="banner-content">
            <h2>Her Ev Güzel Mobilyalarla</h2>
            <p>Şıklığı ve konforu bir arada sunan mobilyalarla evinizi güzelleştirin.</p>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="main-nav">
        <div class="container">
            <ul class="main-menu">
                <li class="menu-item"><a href="#">Oturma Odası</a></li>
                <li class="menu-item"><a href="#">Yatak Odası</a></li>
                <li class="menu-item"><a href="#">Yemek Odası</a></li>
                <li class="menu-item"><a href="#">Genç Odası</a></li>
                <li class="menu-item"><a href="#">Bahçe</a></li>
                <li class="menu-item"><a href="#">Blog</a></li>
            </ul>
        </div>
    </nav>

    <!-- Featured Products -->
    <section class="featured-products">
        <div class="container">
            <div class="section-header">
                <h2>Öne Çıkan Ürünler</h2>
            </div>
            <div class="row">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/bahçe1.jpg" alt="Ürün 1" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Bahçe Takımı</a></h3>
                            <div class="product-price">
                                <span class="current-price">10,290.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="1"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/yatako1.jpg" alt="Ürün 2" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Yatak Odası Takımı</a></h3>
                            <div class="product-price">
                                <span class="current-price">10,703.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="2"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/oturmao1.jpg" alt="Ürün 3" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Oturma Odası Takımı</a></h3>
                            <div class="product-price">
                                <span class="current-price">18,427.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="3"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/genço1.jpg" alt="Ürün 4" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Genç Odası Takımı</a></h3>
                            <div class="product-price">
                                <span class="current-price">9,999.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="4"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/oturmao2.jpg" alt="Ürün 5" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Oturma Odası Takımı</a></h3>
                            <div class="product-price">
                                <span class="current-price">10,703.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="2"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/genço2.jpg" alt="Ürün 6" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Genç Odası Takımı</a></h3>
                            <div class="product-price">
                                <span class="current-price">10,703.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="2"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                   <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/bahçe2.jpg" alt="Ürün 7" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Bahçe Salıncağı</a></h3>
                            <div class="product-price">
                                <span class="current-price">10,703.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="2"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
                   <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="product-thumb">
                            <a href="product-details.php">
                                <img src="resimler/yatako2.jpg" alt="Ürün 8" class="img-fluid">
                            </a>
                        </div>
                        <div class="product-info">
                            <h3 class="product-title"><a href="product-details.php">Yatak Odası Takımı</a></h3>
                            <div class="product-price">
                                <span class="current-price">10,703.00 TL</span>
                            </div>
                            <form method="POST" class="add-to-cart-form">
                                <input type="hidden" name="product_id" value="2"> <!-- Ürün ID'si -->
                                <button type="submit" class="btn btn-primary btn-add-to-cart">Sepete Ekle</button>
                            </form>
                        </div>
                    </div>
                </div>
            
    </section>

    <!-- Contact Information -->
    <div class="contact-info">
        <div class="container">
            <div class="info-item">
                <i class="fas fa-phone"></i> <a href="tel:08502223344">0850 222 33 44</a>
            </div>
            <div class="info-item">
                <i class="fab fa-whatsapp"></i> <a href="#">WhatsApp İletişim</a>
            </div>
            <div class="info-item">
                <i class="fas fa-download"></i> <a href="#">Uygulamamız İndirin</a>
            </div>
        </div>
    </div>

    <!-- Footer Links -->
    <footer class="footer">
        <div class="container footer-links">
            <div class="row">
                <div class="col-md-4">
                    <h5>Kurumsal</h5>
                    <ul>
                        <li><a href="#">İletişim</a></li>
                        <li><a href="#">Hakkımızda</a></li>
                        <li><a href="#">İş Fırsatları</a></li>
                        <li><a href="#">Sıkça Sorulan Sorular</a></li>
                        <li><a href="#">Mobil Uygulama</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Oturma Odası</h5>
                    <ul>
                        <li><a href="#">Koltuk Takımları</a></li>
                        <li><a href="#">Köşe Takımları</a></li>
                        <li><a href="#">Berjerler</a></li>
                        <li><a href="#">Puflar</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Yemek Odası</h5>
                    <ul>
                        <li><a href="#">Yemek Odası Takımları</a></li>
                        <li><a href="#">Konsol</a></li>
                        <li><a href="#">Korsel Ayna</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Yatak Odası</h5>
                    <ul>
                        <li><a href="#">Yatak Odası Takımları</a></li>
                        <li><a href="#">Yataklar</a></li>
                        <li><a href="#">Başlıklar</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Çocuk ve Genç Odası</h5>
                    <ul>
                        <li><a href="#">Genç Odası Takımları</a></li>
                        <li><a href="#">Bebek Yatakları</a></li>
                        <li><a href="#">Gece Lambaları</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Bahçe Mobilyası</h5>
                    <ul>
                        <li><a href="#">Bahçe Takımları</a></li>
                        <li><a href="#">Şezlong</a></li>
                        <li><a href="#">Masa ve Sandalyeler</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sepete ekleme işlemi için AJAX
        document.querySelectorAll('.add-to-cart-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault(); // Formun normal gönderimini engelle
                const formData = new FormData(this);
                fetch('', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    // Sepet sayısını güncelle
                    document.querySelector('.cart-count').textContent = data;
                    alert('Sepete eklendi!'); // Kullanıcıya bilgi ver
                })
                .catch(error => console.error('Hata:', error));
            });
        });
    </script>
</body>
</html>