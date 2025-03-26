<?php
// Veritabanı bağlantı bilgileri
define('DB_SERVER', 'sql107.infinityfree.com');
define('DB_USERNAME', 'if0_37730032');
define('DB_PASSWORD', 'FLxhachSVKMHgJ');
define('DB_NAME', 'if0_37730032_meryem');

// PDO ile veritabanı bağlantısı
try {
    $conn = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    // PDO hata modunu ayarla
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Türkçe karakter sorunlarını önlemek için karakter setini ayarla
    $conn->exec("SET NAMES 'utf8'");
    $conn->exec("SET CHARACTER SET utf8");
    $conn->exec("SET COLLATION_CONNECTION = 'utf8_general_ci'");
} catch(PDOException $e) {
    die("Veritabanı bağlantı hatası: " . $e->getMessage());
}
?>