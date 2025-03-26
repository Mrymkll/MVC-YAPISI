<?php
session_start(); // Oturum başlat
session_destroy(); // Oturumu sonlandır
header("Location: /meryem/index.php"); // Ana sayfaya yönlendir
exit;
?>