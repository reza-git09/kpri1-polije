```php
<?php 
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../user/login.php");
    exit;
}

include '../config/koneksi.php'; 

$active_menu = 'laporan';
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/kpri%20polije/";

$jenis_laporan = $_GET['jenis_laporan'] ?? 'pinjaman'; 
$filter_type   = $_GET['filter_type'] ?? 'semua';
$tgl_pilih     = $_GET['tgl'] ?? date('Y-m-d');
$bulan_pilih   = $_GET['bulan'] ?? date('m');
$tahun_pilih   = $_GET['tahun'] ?? date('Y');

// ================= QUERY DATA =================
if ($jenis_laporan == 'pinjaman') {

    $judul_h4 = "Laporan Pinjaman Barang";

    $query_str = "SELECT p.*, p.status AS status_pinjam, a.nama, a.id_anggota, b.nama_barang 
                  FROM tb_pinjaman p 
                  JOIN tb_anggota a ON p.id_anggota = a.id_anggota 
                  JOIN tb_barang b ON p.id_barang = b.id_barang 
                  WHERE 1=1";

    $date_field = "p.tanggal";

} elseif ($jenis_laporan == 'simpanan') {

    $judul_h4 = "Laporan Simpanan Anggota";

    $query_str = "SELECT s.*, a.nama, a.id_anggota 
                  FROM tb_simpanan s 
                  JOIN tb_anggota a ON s.id_anggota = a.id_anggota 
                  WHERE 1=1";

    $date_field = "s.tanggal";

} elseif ($jenis_laporan == 'gudang') {

    $judul_h4 = "Laporan Gudang";

    $query_str = "SELECT * FROM tb_barang WHERE 1=1";

    $date_field = "id_barang";
}

// ================= FILTER =================
if ($jenis_laporan != 'gudang') {

    if ($filter_type == 'harian') {

        $query_str .= " AND $date_field = '$tgl_pilih'";

    } elseif ($filter_type == 'bulanan') {

        $query_str .= " AND MONTH($date_field) = '$bulan_pilih' 
                        AND YEAR($date_field) = '$tahun_pilih'";
    }
}

$query_str .= " ORDER BY $date_field DESC";

$data_laporan = mysqli_query($koneksi, $query_str);

// ================= EXPORT EXCEL =================
if (isset($_GET['export']) && $_GET['export'] == 'excel') {

    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=Laporan_KPRI_Polije.xls");
    header("Pragma: no-cache");
    header("Expires: 0");

    echo "<table border='1'>";

    if ($jenis_laporan == 'gudang') {
        $colspan = 5;
    } elseif ($jenis_laporan == 'pinjaman') {
        $colspan = 6;
    } else {
        $colspan = 6;
    }

    echo "<tr>
            <th colspan='$colspan' style='font-size:18px; font-weight:bold;'>
                $judul_h4
            </th>
          </tr>";

    echo "<tr>
            <th colspan='$colspan'>KPRI POLIJE</th>
          </tr>";

    echo "<tr>
            <th colspan='$colspan'>
                Dicetak pada : ".date('d F Y')."
            </th>
          </tr>";

    echo "<tr></tr>";

    if ($jenis_laporan == 'pinjaman') {

        echo "<tr>
                <th>No</th>
                <th>ID Anggota</th>
                <th>Tanggal Pinjam</th>
                <th>Nama Anggota</th>
                <th>Barang</th>
                <th>Status</th>
              </tr>";

    } elseif ($jenis_laporan == 'simpanan') {

        echo "<tr>
                <th>No</th>
                <th>ID Anggota</th>
                <th>Tanggal</th>
                <th>Nama Anggota</th>
                <th>Jenis Simpanan</th>
                <th>Nominal</th>
              </tr>";

    } elseif ($jenis_laporan == 'gudang') {

        echo "<tr>
                <th>No</th>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Stok</th>
                <th>Satuan</th>
              </tr>";
    }

    $no = 1;

    while ($row = mysqli_fetch_assoc($data_laporan)) {

        echo "<tr>";

        echo "<td>".$no++."</td>";

        if ($jenis_laporan == 'pinjaman') {

            echo "
                <td>".$row['id_anggota']."</td>
                <td>".date('d/m/Y', strtotime($row['tanggal']))."</td>
                <td>".$row['nama']."</td>
                <td>".$row['nama_barang']."</td>
                <td>".$row['status_pinjam']."</td>
            ";

        } elseif ($jenis_laporan == 'simpanan') {

            echo "
                <td>".$row['id_anggota']."</td>
                <td>".date('d/m/Y', strtotime($row['tanggal']))."</td>
                <td>".$row['nama']."</td>
                <td>".$row['jenis_simpanan']."</td>
                <td>".$row['nominal']."</td>
            ";

        } elseif ($jenis_laporan == 'gudang') {

            echo "
                <td>".$row['id_barang']."</td>
                <td>".$row['nama_barang']."</td>
                <td>".$row['stok']."</td>
                <td>".$row['satuan']."</td>
            ";
        }

        echo "</tr>";
    }

    echo "</table>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <title>Laporan - KPRI POLIJE</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>

        :root {
            --primary: #2563eb;
            --bg-body: #f1f5f9;
            --sidebar-dark: #2c3e50;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-body);
            display: flex;
        }

        .sidebar {
            width: 260px;
            background: var(--sidebar-dark);
            min-height: 100vh;
            color: white;
            position: fixed;
        }

        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 30px;
            width: calc(100% - 260px);
        }

        .header-panel {
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border-radius: 20px;
            padding: 25px 35px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.2);
        }

        .header-title h2 {
            font-size: 26px;
            font-weight: 600;
        }

        .content-card {
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .select-custom {
            padding: 10px 15px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            outline: none;
            font-family: 'Poppins';
        }

        .btn-group {
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 22px;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-print {
            background: #2563eb;
            color: white;
        }

        .btn-excel {
            background: #10b981;
            color: white;
        }

        .search-input{
            padding:10px 15px;
            border-radius:10px;
            border:1px solid #e2e8f0;
            outline:none;
            font-family:'Poppins';
            width:250px;
        }

        .info-laporan {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:20px;
            flex-wrap:wrap;
            gap:10px;
        }

        .total-data {
            background:#eff6ff;
            color:#2563eb;
            padding:12px 18px;
            border-radius:12px;
            font-weight:600;
        }

        .tanggal-cetak {
            color:#64748b;
            font-size:14px;
            font-weight:500;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
        }

        thead th {
            background: #f8fafc;
            color: #64748b;
            padding: 15px;
            text-align: left;
            font-size: 13px;
            border-bottom: 2px solid #f1f5f9;
        }

        tbody tr{
            transition:0.2s;
        }

        tbody tr:hover{
            transform:scale(1.01);
        }

        tbody td {
            padding: 18px 15px;
            background: white;
        }

        .no-index {
            background: #eff6ff;
            color: #2563eb;
            padding: 6px 12px;
            border-radius: 8px;
            font-weight: 700;
        }

        .status-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-danger {
            background: #fee2e2;
            color: #ef4444;
        }

        .status-success {
            background: #dcfce7;
            color: #10b981;
        }

        .stok-badge {
            padding:6px 14px;
            border-radius:20px;
            font-weight:700;
            font-size:13px;
        }

        @media print {

            .sidebar,
            .btn-group,
            .search-box,
            .action-bar {
                display:none !important;
            }

            .main-content{
                margin:0;
                width:100%;
                padding:0;
            }

            .content-card{
                box-shadow:none;
            }

            body{
                background:white;
            }

        }

    </style>

</head>

<body>

<div class="sidebar">
    <?php include '../layout/sidebar.php'; ?>
</div>

<div class="main-content">

    <div class="header-panel">

        <div class="header-title">
            <h2>
                <i class="fas fa-chart-line"></i>
                <?= $judul_h4; ?>
            </h2>
        </div>

    </div>

    <div class="content-card">

        <div class="action-bar">

            <form method="GET">

                <select name="jenis_laporan" class="select-custom" onchange="this.form.submit()">

                    <option value="pinjaman" <?= $jenis_laporan == 'pinjaman' ? 'selected' : ''; ?>>
                        Laporan Pinjaman
                    </option>

                    <option value="simpanan" <?= $jenis_laporan == 'simpanan' ? 'selected' : ''; ?>>
                        Laporan Simpanan
                    </option>

                    <option value="gudang" <?= $jenis_laporan == 'gudang' ? 'selected' : ''; ?>>
                        Laporan Gudang
                    </option>

                </select>

            </form>

            <div style="display:flex; gap:15px; align-items:center; flex-wrap:wrap;">

                <div class="search-box">

                    <input 
                        type="text" 
                        id="searchInput"
                        class="search-input"
                        placeholder="Cari data..."
                    >

                </div>

                <div class="btn-group">

                    <button onclick="window.print()" class="btn btn-print">
                        <i class="fas fa-print"></i> Cetak
                    </button>

                    <a href="?<?= $_SERVER['QUERY_STRING']; ?>&export=excel" class="btn btn-excel">
                        <i class="fas fa-file-excel"></i> Excel
                    </a>

                </div>

            </div>

        </div>

        <div class="info-laporan">

            <div class="total-data">
                <i class="fas fa-box"></i>
                Total Data :
                <?= mysqli_num_rows($data_laporan); ?>
            </div>

            <div class="tanggal-cetak">
                <i class="fas fa-calendar"></i>
                Dicetak :
                <?= date('d F Y'); ?>
            </div>

        </div>

        <table>

            <thead>

            <?php if($jenis_laporan == 'pinjaman'): ?>

                <tr>
                    <th>No</th>
                    <th>ID Anggota</th>
                    <th>Tanggal Pinjam</th>
                    <th>Nama Anggota</th>
                    <th>Barang</th>
                    <th>Status</th>
                </tr>

            <?php elseif($jenis_laporan == 'simpanan'): ?>

                <tr>
                    <th>No</th>
                    <th>ID Anggota</th>
                    <th>Tanggal</th>
                    <th>Nama Anggota</th>
                    <th>Jenis Simpanan</th>
                    <th>Nominal</th>
                </tr>

            <?php elseif($jenis_laporan == 'gudang'): ?>

                <tr>
                    <th>No</th>
                    <th>ID Barang</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                </tr>

            <?php endif; ?>

            </thead>

            <tbody>

            <?php 
            $no = 1;

            if(mysqli_num_rows($data_laporan) > 0):

            while($row = mysqli_fetch_assoc($data_laporan)):
            ?>

            <tr>

                <td>
                    <span class="no-index"><?= $no++; ?></span>
                </td>

                <?php if($jenis_laporan == 'pinjaman'): ?>

                    <td><?= $row['id_anggota']; ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['nama_barang']; ?></td>

                    <td>

                        <?php 
                        $s = strtolower(trim($row['status_pinjam']));
                        $class = ($s == 'dipinjam') ? 'status-danger' : 'status-success';
                        ?>

                        <span class="status-badge <?= $class; ?>">
                            <?= $row['status_pinjam']; ?>
                        </span>

                    </td>

                <?php elseif($jenis_laporan == 'simpanan'): ?>

                    <td><?= $row['id_anggota']; ?></td>
                    <td><?= date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                    <td><?= $row['nama']; ?></td>
                    <td><?= $row['jenis_simpanan']; ?></td>

                    <td style="font-weight:600; color:#10b981;">
                        Rp <?= number_format($row['nominal'],0,',','.'); ?>
                    </td>

                <?php elseif($jenis_laporan == 'gudang'): ?>

                    <td><?= $row['id_barang']; ?></td>
                    <td><?= $row['nama_barang']; ?></td>

                    <?php
                    $stok = $row['stok'];

                    if($stok <= 10){
                        $warna = '#ef4444';
                        $bg = '#fee2e2';
                    } elseif($stok <= 20){
                        $warna = '#f59e0b';
                        $bg = '#fef3c7';
                    } else {
                        $warna = '#10b981';
                        $bg = '#dcfce7';
                    }
                    ?>

                    <td>

                        <span class="stok-badge" style="
                            background:<?= $bg ?>;
                            color:<?= $warna ?>;
                        ">

                            <?= $stok; ?>

                        </span>

                    </td>

                    <td><?= $row['satuan']; ?></td>

                <?php endif; ?>

            </tr>

            <?php endwhile; else: ?>

            <tr>

                <td colspan="<?= ($jenis_laporan == 'gudang') ? '5' : '6'; ?>" 
                    style="text-align:center; padding:40px; color:#94a3b8;">

                    Data tidak ditemukan.

                </td>

            </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script>

const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('keyup', function(){

    const keyword = this.value.toLowerCase();

    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(function(row){

        const text = row.innerText.toLowerCase();

        if(text.includes(keyword)){
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }

    });

});

</script>

</body>
</html>
```
