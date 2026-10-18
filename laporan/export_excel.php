```php
<?php
// 🔥 WAJIB PALING ATAS
while (ob_get_level()) {
    ob_end_clean();
}

ob_start();

session_start();
include '../config/koneksi.php';

// 🔒 Proteksi Login
if (!isset($_SESSION['login'])) {
    exit("Akses ditolak.");
}

// ================= PARAMETER =================
$jenis_laporan = $_GET['jenis_laporan'] ?? 'pinjaman';
$filter_type   = $_GET['filter_type'] ?? 'semua';
$tgl_pilih     = $_GET['tgl'] ?? date('Y-m-d');
$bulan_pilih   = $_GET['bulan'] ?? date('m');
$tahun_pilih   = $_GET['tahun'] ?? date('Y');

// ================= NAMA FILE =================
$filename = "Laporan_" . ucfirst($jenis_laporan) . "_" . date('Ymd_His') . ".xls";

// ================= HEADER EXCEL =================
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Pragma: no-cache");
header("Expires: 0");

// Bersihkan buffer sebelum output
ob_clean();

// BOM UTF-8
echo "\xEF\xBB\xBF";


// ================= QUERY =================
if ($jenis_laporan == 'pinjaman') {

    $judul = "LAPORAN PINJAMAN BARANG KPRI POLIJE";

    $query_str = "SELECT p.*, 
                         a.nama, 
                         b.nama_barang, 
                         b.harga 
                  FROM tb_pinjaman p 
                  JOIN tb_anggota a ON p.id_anggota = a.id_anggota 
                  JOIN tb_barang b ON p.id_barang = b.id_barang 
                  WHERE 1=1";

    $date_field = "p.tanggal";

} elseif ($jenis_laporan == 'simpanan') {

    $judul = "LAPORAN SIMPANAN ANGGOTA KPRI POLIJE";

    $query_str = "SELECT s.*, 
                         a.nama 
                  FROM tb_simpanan s 
                  JOIN tb_anggota a ON s.id_anggota = a.id_anggota 
                  WHERE 1=1";

    $date_field = "s.tanggal";

} elseif ($jenis_laporan == 'gudang') {

    $judul = "LAPORAN GUDANG KPRI POLIJE";

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


// ================= COLSPAN =================
if ($jenis_laporan == 'pinjaman') {
    $colspan = 7;
} elseif ($jenis_laporan == 'simpanan') {
    $colspan = 5;
} else {
    $colspan = 5;
}


// ================= OUTPUT EXCEL =================
echo "<table border='1' cellpadding='5' cellspacing='0'>";


// ================= JUDUL =================
echo "
<tr>
    <th colspan='$colspan'
        style='font-size:16px;font-weight:bold;text-align:center;'>
        $judul
    </th>
</tr>";

echo "
<tr>
    <th colspan='$colspan'
        style='text-align:center;'>
        Dicetak pada: ".date('d/m/Y H:i:s')."
    </th>
</tr>";

echo "<tr></tr>";


// ================= HEADER TABEL =================
echo "
<tr style='background-color:#4a86e8; color:white; text-align:center;'>
    <th>No</th>";

if ($jenis_laporan == 'pinjaman') {

    echo "
    <th>Tanggal</th>
    <th>Nama Anggota</th>
    <th>Barang</th>
    <th>Harga</th>
    <th>Jumlah</th>
    <th>Status</th>";

} elseif ($jenis_laporan == 'simpanan') {

    echo "
    <th>Tanggal</th>
    <th>Nama Anggota</th>
    <th>Jenis Simpanan</th>
    <th>Nominal</th>";

} elseif ($jenis_laporan == 'gudang') {

    echo "
    <th>ID Barang</th>
    <th>Nama Barang</th>
    <th>Stok</th>
    <th>Satuan</th>";
}

echo "</tr>";


// ================= DATA =================
$no = 1;
$total_nominal = 0;

if(mysqli_num_rows($data_laporan) > 0):

    while($row = mysqli_fetch_assoc($data_laporan)):

        echo "<tr>";

        echo "<td>".$no++."</td>";

        if ($jenis_laporan == 'pinjaman') {

            echo "
            <td>".date('d/m/Y', strtotime($row['tanggal']))."</td>

            <td>".htmlspecialchars($row['nama'])."</td>

            <td>".htmlspecialchars($row['nama_barang'])."</td>

            <td style=\"mso-number-format:'#,##0';\">
                Rp ".number_format($row['harga'], 0, ',', '.')."
            </td>

            <td>".$row['jumlah']."</td>

            <td>".ucfirst($row['status'])."</td>";

        } elseif ($jenis_laporan == 'simpanan') {

            $total_nominal += $row['nominal'];

            echo "
            <td>".date('d/m/Y', strtotime($row['tanggal']))."</td>

            <td>".htmlspecialchars($row['nama'])."</td>

            <td>Simpanan ".htmlspecialchars($row['jenis_simpanan'])."</td>

            <td style=\"mso-number-format:'#,##0';\">
                Rp ".number_format($row['nominal'], 0, ',', '.')."
            </td>";

        } elseif ($jenis_laporan == 'gudang') {

            echo "
            <td>".$row['id_barang']."</td>

            <td>".htmlspecialchars($row['nama_barang'])."</td>

            <td>".$row['stok']."</td>

            <td>".$row['satuan']."</td>";
        }

        echo "</tr>";

    endwhile;


    // ================= TOTAL SIMPANAN =================
    if($jenis_laporan == 'simpanan') {

        echo "
        <tr>

            <td colspan='4'
                style='text-align:right;font-weight:bold;'>
                TOTAL
            </td>

            <td style='font-weight:bold;'>
                Rp ".number_format($total_nominal, 0, ',', '.')."
            </td>

        </tr>";
    }

else:

    echo "
    <tr>
        <td colspan='$colspan'
            style='text-align:center;'>
            Data tidak ditemukan
        </td>
    </tr>";

endif;


// ================= PENUTUP =================
echo "</table>";

exit;
?>
```
