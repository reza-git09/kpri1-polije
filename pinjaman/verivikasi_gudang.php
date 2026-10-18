```php
<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../user/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    header("Location: lihat.php");
    exit;
}

$id_pinjaman = mysqli_real_escape_string($koneksi, $_GET['id']);

$query = mysqli_query($koneksi,"
    SELECT p.*, b.id_barang
    FROM tb_pinjaman p
    JOIN tb_barang b ON p.id_barang = b.id_barang
    WHERE p.id_pinjaman = '$id_pinjaman'
");

$data = mysqli_fetch_assoc($query);

if (!$data) {
    die("Data tidak ditemukan");
}

if ($data['status'] != 'Menunggu Verifikasi Gudang') {
    die("Status tidak valid");
}

$id_barang = $data['id_barang'];
$jumlah    = $data['jumlah'];

mysqli_begin_transaction($koneksi);

try {

    // Tambah stok barang
    mysqli_query($koneksi,"
        UPDATE tb_barang
        SET stok = stok + $jumlah
        WHERE id_barang = '$id_barang'
    ");

    // Ubah status menjadi selesai
    mysqli_query($koneksi,"
        UPDATE tb_pinjaman
        SET status = 'Selesai'
        WHERE id_pinjaman = '$id_pinjaman'
    ");

    mysqli_commit($koneksi);

    echo "
    <script>
        alert('Verifikasi gudang berhasil. Stok telah ditambahkan.');
        window.location='lihat.php';
    </script>
    ";

} catch(Exception $e){

    mysqli_rollback($koneksi);

    echo "
    <script>
        alert('Gagal verifikasi gudang.');
        window.location='lihat.php';
    </script>
    ";
}
?>
```