<?php
// 🔥 MATIKAN SIDEBAR SAAT EXPORT EXCEL
if (defined('EXPORT_MODE')) return;

// Pastikan sesi aktif
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/kpri%20polije/";
$role = $_SESSION['role'] ?? 'admin';

// Pendeteksian URL aktif yang presisi
$current_uri = $_SERVER['REQUEST_URI'];
$current_file = basename($_SERVER['PHP_SELF']);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
    .sidebar {
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif !important;
        margin: 0;
        padding: 0;
        width: 260px;
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        background: #273647;
        display: flex;
        flex-direction: column;
        z-index: 1000;
        color: white;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    }

    .sidebar h3, .sidebar small, .sidebar span, .sidebar a, .sidebar div {
        font-family: 'Poppins', sans-serif !important;
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    .sidebar i, .sidebar i::before {
        font-family: "Font Awesome 6 Free" !important;
        font-weight: 900 !important;
        display: inline-block;
        font-style: normal;
        font-variant: normal;
        text-rendering: auto;
        -webkit-font-smoothing: antialiased;
    }

    .sidebar-brand {
        text-align: center;
        padding: 25px 20px !important;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }

    .logo-box {
        width: 72px;
        height: 72px;
        background: white;
        border-radius: 16px;
        margin: 0 auto 14px auto !important;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-box img {
        width: 48px;
        height: auto;
        display: block;
        margin: 0;
        padding: 0;
    }

    .sidebar-brand h3 {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 5px !important;
        color: white;
        line-height: 1.2;
    }

    .sidebar-brand small {
        color: #b9c6d3;
        font-size: 13px;
        font-weight: 400;
        display: block;
    }

    .nav-menu {
        flex: 1;
        padding: 20px 15px !important;
        overflow-y: auto;
    }

    .nav-menu::-webkit-scrollbar {
        width: 4px;
    }

    .nav-menu::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.1);
        border-radius: 10px;
    }

    .menu-header {
        color: #7f8c8d;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        margin: 20px 0 12px 10px !important;
        display: block;
        letter-spacing: 0.5px;
    }

    .nav-link-custom {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 14px 16px !important;
        margin-bottom: 8px !important;
        border-radius: 12px;
        text-decoration: none;
        color: #bfc9d4;
        font-size: 15px;
        font-weight: 500;
        transition: all 0.3s ease;
        line-height: 1;
    }

    .nav-link-custom i {
        width: 20px;
        text-align: center;
        font-size: 17px;
    }

    .nav-link-custom:hover {
        background: rgba(255,255,255,0.08);
        color: white;
    }

    .nav-link-custom.active {
        background: #2d74da;
        color: white;
        font-weight: 600;
    }

    .sidebar-footer {
        padding: 20px 15px !important; 
        border-top: 1px solid rgba(255,255,255,0.05);
        background: #23303f;
    }

    .btn-logout {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        background: #d94336;
        color: white;
        padding: 12px 16px !important; 
        width: 85%;          
        margin: 0 auto 15px auto !important; 
        border-radius: 12px; 
        text-decoration: none;
        font-weight: 600;
        font-size: 15px; 
        transition: all 0.3s ease;
    }

    .btn-logout:hover {
        background: #bb2d20;
        color: white;
    }

    .copyright {
        text-align: center;
        font-size: 11px;
        color: #7f8c8d;
        line-height: 1.3;
    }
</style>

<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="logo-box">
            <img src="<?= $base_url ?>images/kpripolije.png" alt="Logo KPRI">
        </div>
        <h3>KPRI POLIJE</h3>
        <small>LOGIN: <?= strtoupper($role) ?></small>
    </div>

    <nav class="nav-menu">

        <?php if($role !== 'manager'): ?>
        <a href="<?= $base_url ?>index.php"
           class="nav-link-custom <?= ($current_file == 'index.php') ? 'active' : '' ?>">
            <i class="fa-solid fa-house"></i>
            Dashboard
        </a>
        <?php endif; ?>

        <span class="menu-header">Data Master</span>

        <a href="<?= $base_url ?>anggota/lihat.php"
           class="nav-link-custom <?= (strpos($current_uri, '/anggota/') !== false) ? 'active' : '' ?>">
            <i class="fa-solid fa-users"></i>
            Data Anggota
        </a>

        <a href="<?= $base_url ?>simpanan/lihat.php"
           class="nav-link-custom <?= (strpos($current_uri, '/simpanan/') !== false) ? 'active' : '' ?>">
            <i class="fa-solid fa-wallet"></i>
            Simpanan
        </a>

        <a href="<?= $base_url ?>pinjaman/lihat.php"
           class="nav-link-custom <?= (strpos($current_uri, '/pinjaman/') !== false) ? 'active' : '' ?>">
            <i class="fa-solid fa-hand-holding-dollar"></i>
            Pinjaman
        </a>

        <a href="<?= $base_url ?>barang/lihat.php"
           class="nav-link-custom <?= (strpos($current_uri, '/barang/') !== false) ? 'active' : '' ?>">
            <i class="fa-solid fa-box"></i>
            Stok Barang
        </a>

        <span class="menu-header">Laporan</span>

        <a href="<?= $base_url ?>laporan/lihat.php"
           class="nav-link-custom <?= (strpos($current_uri, '/laporan/') !== false) ? 'active' : '' ?>">
            <i class="fa-solid fa-file-lines"></i>
            Laporan KPRI
        </a>

    </nav>

    <div class="sidebar-footer">
        <a href="<?= $base_url ?>user/logout.php" class="btn-logout">
            <i class="fa-solid fa-right-from-bracket"></i>
            KELUAR
        </a>

        <div class="copyright">
            © <?= date('Y') ?> Design by Syntax Error Polije
        </div>
    </div>

</aside>