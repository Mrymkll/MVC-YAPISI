<?php
session_start(); // Oturum başlat
session_destroy(); // Oturumu sonlandır
header("Location: /yusuf/index.php"); // Ana sayfaya yönlendir
exit;
?>