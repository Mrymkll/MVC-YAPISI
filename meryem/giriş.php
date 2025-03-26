<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Veritabanı bağlantısı
$servername = "sql107.infinityfree.com";
$username = "if0_37730032";
$password = "FLxhachSVKMHgJ"; // Şifrenizi buraya doğru girdiğinizden emin olun
$dbname = "if0_37730032_meryem";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Bağlantı hatası: " . $e->getMessage());
}

// Kullanıcı kayıt işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['kayit'])) {
    $adsoyad = $_POST['adsoyad'];
    $eposta = $_POST['eposta'];
    $sifre = password_hash($_POST['sifre'], PASSWORD_DEFAULT); // Şifreyi hashle
    $adres = $_POST['adres'];

    try {
        $query = "INSERT INTO kullanicilar (adsoyad, eposta, sifre, adres) VALUES (:adsoyad, :eposta, :sifre, :adres)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':adsoyad', $adsoyad);
        $stmt->bindParam(':eposta', $eposta);
        $stmt->bindParam(':sifre', $sifre);
        $stmt->bindParam(':adres', $adres);
        $stmt->execute();

        $successMessage = "Kayıt başarıyla gerçekleşti.";
    } catch (PDOException $e) {
        $errorMessage = "Veritabanı hatası: " . $e->getMessage();
    }
}

// Giriş yapma işlemi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['giris'])) {
    $eposta = $_POST['eposta'];
    $sifre = $_POST['sifre'];
    
    try {
        $query = "SELECT * FROM kullanicilar WHERE eposta = :eposta";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':eposta', $eposta);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && password_verify($sifre, $user['sifre'])) {
            $_SESSION['loggedin'] = true;
            $_SESSION['eposta'] = $eposta;
            header("Location: ../site2/index.php");
            exit;
        } else {
            $errorMessage = "E-posta veya şifre hatalı.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Veritabanı hatası: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap / Kayıt Ol</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2b3c6a;
            --secondary-color: #e9b949;
        }
        
        body {
            background-color: #f8f9fa;
        }
        
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: #1e2d54;
            border-color: #1e2d54;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <!-- Hata ve Başarı Mesajları -->
            <?php if(isset($errorMessage)): ?>
                <div class="alert alert-danger"><?php echo $errorMessage; ?></div>
            <?php endif; ?>
            
            <?php if(isset($successMessage)): ?>
                <div class="alert alert-success"><?php echo $successMessage; ?></div>
            <?php endif; ?>

            <!-- Giriş Formu -->
            <div class="mb-4">
                <h3 class="text-center mb-4">Giriş Yap</h3>
                <form method="POST" action="">
                    <div class="mb-3">
                        <label for="eposta" class="form-label">E-posta</label>
                        <input type="email" class="form-control" id="eposta" name="eposta" required>
                    </div>
                    <div class="mb-3">
                        <label for="sifre" class="form-label">Şifre</label>
                        <input type="password" class="form-control" id="sifre" name="sifre" required>
                    </div>
                    <button type="submit" name="giris" class="btn btn-primary w-100">Giriş Yap</button>
                </form>
            </div>

            <hr>

            <!-- Kayıt Ol Butonu -->
            <div class="text-center">
                <p>Hesabınız yok mu?</p>
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#kayitModal">
                    Kayıt Ol
                </button>
            </div>
        </div>
    </div>

    <!-- Kayıt Modal -->
    <div class="modal fade" id="kayitModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Hesap Oluştur</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="kayit-adsoyad" class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" id="kayit-adsoyad" name="adsoyad" required>
                        </div>
                        <div class="mb-3">
                            <label for="kayit-eposta" class="form-label">E-posta</label>
                            <input type="email" class="form-control" id="kayit-eposta" name="eposta" required>
                        </div>
                        <div class="mb-3">
                            <label for="kayit-sifre" class="form-label">Şifre</label>
                            <input type="password" class="form-control" id="kayit-sifre" name="sifre" required>
                        </div>
                        <div class="mb-3">
                            <label for="kayit-adres" class="form-label">Adres</label>
                            <textarea class="form-control" id="kayit-adres" name="adres" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="kayit" class="btn btn-primary w-100">Kayıt Ol</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>