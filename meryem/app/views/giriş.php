<?php
// Veritabanı bağlantısı için gerekli bilgiler
$servername = "sql112.infinityfree.com";  
$username = "if0_37711681";                
$password = "gU3BMuChh2JKC";                
$dbname = "if0_37711681_denemedb2121";         

// Veritabanı bağlantısı
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantı kontrolü
if ($conn->connect_error) {
    die("Veritabanı bağlantısı başarısız: " . $conn->connect_error);
}

// Karakter setini ayarla
$conn->set_charset("utf8mb4");

session_start(); // Oturum başlat

// Hata raporlamasını aç
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Giriş işlemi kontrolü
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // SQL sorgusu - MÜŞTERİLER tablosunu kullan
    $stmt = $conn->prepare("SELECT ad FROM MÜŞTERİLER WHERE eposta = ? AND sifre = ?");
    
    if ($stmt === false) {
        die("SQL sorgusu hazırlanmada hata: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->store_result();

    // Kullanıcı varsa
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($ad);
        $stmt->fetch();
        $_SESSION['adsoyad'] = $ad; // Oturum değişken adını eski haliyle koruduk
        header("Location: /yusuf/index.php"); // Ana sayfaya yönlendir
        exit;
    } else {
        $error_message = "Hatalı e-posta veya şifre! Lütfen tekrar deneyin.";
    }
}

// Kayıt işlemi kontrolü
if (isset($_POST['register'])) {
    $eposta = $_POST['regEmail'];
    $ad = $_POST['regName'];
    $sifre = $_POST['regPassword'];
    $telefon = $_POST['regPhone']; // Yeni telefon alanı

    // Veritabanına ekle
    $stmt = $conn->prepare("INSERT INTO MÜŞTERİLER (eposta, ad, sifre, telefon) VALUES (?, ?, ?, ?)");
    
    if ($stmt === false) {
        die("SQL sorgusu hazırlanmada hata: " . $conn->error);
    }
    
    $stmt->bind_param("ssss", $eposta, $ad, $sifre, $telefon);
    
    if ($stmt->execute()) {
        // Kayıt başarılı
        $_SESSION['adsoyad'] = $ad; // Oturum değişken adını eski haliyle koruduk
        header("Location: /yusuf/index.php");
        exit;
    } else {
        $reg_error_message = "Kayıt sırasında bir hata oluştu: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Üye Girişi</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #FC466B, #3F5EFB);
            color: #333;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 90%;
            max-width: 400px;
            background-color: rgba(255, 245, 250, 0.95);
            padding: 40px 30px;
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            text-align: center;
        }
        
        h2 {
            color: #5B6BF9;
            margin-bottom: 10px;
            font-size: 28px;
            letter-spacing: 1px;
        }
        
        .colorful-divider {
            height: 4px;
            background: linear-gradient(90deg, #e83e8c, #00c3ff, #5BF9A7);
            margin: 15px 0 25px 0;
            border-radius: 5px;
        }

        .button {
            padding: 18px 20px;
            border-radius: 50px;
            border: none;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        .button i {
            margin-right: 12px;
            font-size: 20px;
        }

        .btn-login {
            background: linear-gradient(90deg, #00c3ff, #5BF9A7);
            color: #1a1a1a;
            box-shadow: 0 4px 10px rgba(0, 195, 255, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }
        
        .btn-login:hover:before {
            left: 100%;
        }

        .btn-register {
            background: linear-gradient(90deg, #9370DB, #6A5ACD);
            color: white;
            box-shadow: 0 4px 10px rgba(147, 112, 219, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-register:before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
            transition: 0.5s;
        }
        
        .btn-register:hover:before {
            left: 100%;
        }

        .button:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .footer {
            margin-top: 25px;
            color: #777;
            font-size: 13px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            justify-content: center;
            align-items: center;
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background-color: white;
            padding: 30px;
            border: none;
            width: 90%;
            max-width: 400px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .modal-header h3 {
            color: #5B6BF9;
            margin: 0;
            font-size: 24px;
        }
        
        .close-btn {
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #aaa;
            transition: color 0.3s;
        }
        
        .close-btn:hover {
            color: #333;
        }

        .modal-content input {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 50px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        
        .modal-content input:focus {
            border-color: #5B6BF9;
            box-shadow: 0 0 0 2px rgba(91, 107, 249, 0.2);
        }

        .modal-content button {
            width: 100%;
            padding: 15px;
            margin-top: 20px;
            background: linear-gradient(90deg, #00c3ff, #5BF9A7);
            color: #1a1a1a;
            border: none;
            border-radius: 50px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .modal-content button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 195, 255, 0.4);
        }
        
        .error-message {
            background-color: rgba(255, 0, 0, 0.1);
            color: #d32f2f;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 14px;
        }
        
        @media (max-width: 600px) {
            .button-container, .modal-content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>

    <div class="button-container">
        <h2>HOŞGELDİNİZ</h2>
        <div class="colorful-divider"></div>
        
        <div class="button btn-login" onclick="openModal('loginModal')">
            <i class="fas fa-sign-in-alt"></i> Üye Girişi
        </div>
        <div class="button btn-register" onclick="openModal('registerModal')">
            <i class="fas fa-user-plus"></i> Üye Ol
        </div>
        
        <div class="colorful-divider"></div>
        <div class="footer">
            <p>&copy; <?php echo date('Y'); ?> Tüm Hakları Saklıdır</p>
        </div>
    </div>

    <!-- Modal for Login -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Üye Girişi</h3>
                <span class="close-btn" onclick="closeModal('loginModal')">&times;</span>
            </div>
            
            <form method="post" action="">
                <input type="text" name="email" placeholder="Gmail" required />
                <input type="password" name="password" placeholder="Şifre" required />
                <button type="submit" name="login">Giriş Yap</button>
            </form>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal for Registration -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Üye Ol</h3>
                <span class="close-btn" onclick="closeModal('registerModal')">&times;</span>
            </div>
            
            <form method="post" action="">
                <input type="email" name="regEmail" placeholder="E-posta" required />
                <input type="text" name="regName" placeholder="İsim Soyisim" required />
                <input type="password" name="regPassword" placeholder="Şifre" required />
                <input type="tel" name="regPhone" placeholder="Telefon" required />
                <button type="submit" name="register">Kayıt Ol</button>
            </form>
            
            <?php if (isset($reg_error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $reg_error_message; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }
    </script>
</body>
</html>