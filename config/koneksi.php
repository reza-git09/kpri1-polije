<?php
// ===============================
// KONEKSI DATABASE
// ===============================

$host = "localhost";
$user = "root";
$pass = "";
$db   = "stok_kpri";

// Membuat koneksi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$koneksi) {

    die("
    <div style='
        background:#ffe5e5;
        color:#b30000;
        padding:15px;
        border-radius:8px;
        font-family:Poppins,sans-serif;
        margin:20px;
        border:1px solid red;
    '>

        <h3>Koneksi Database Gagal!</h3>

        <p>
            Error :
            ". mysqli_connect_error() ."
        </p>

    </div>
    ");

}

// Set timezone Indonesia
date_default_timezone_set('Asia/Jakarta');

// Set charset UTF-8
mysqli_set_charset($koneksi, "utf8");
?>