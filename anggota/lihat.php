<?php 
session_start();

// Proteksi halaman
if (!isset($_SESSION['login'])) { 
    header("Location: ../user/login.php"); 
    exit; 
}

include '../config/koneksi.php'; 

// Variabel dasar
$base_url = "http://" . $_SERVER['HTTP_HOST'] . "/kpri polije/"; 
$role_saat_ini = $_SESSION['role'];

// Query data: Diurutkan berdasarkan id_anggota
$query_sql = "SELECT * FROM tb_anggota ORDER BY id_anggota ASC";
$query = mysqli_query($koneksi, $query_sql);
?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Anggota - KPRI POLIJE</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="icon" type="image/png" href="<?= $base_url ?>images/kpripolije.png">

    <style>

        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f4f7fe;
            color: #333;
        }

        .main-wrapper {
            margin-left: 260px;
            padding: 25px;
            box-sizing: border-box;
            min-height: 100vh;
        }
        
        .top-header { 
            display: flex;
            justify-content: space-between;
            align-items: center; 
            background: white;
            padding: 15px 25px;
            height: 50px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
        }

        .header-logos img {
            height: 40px;
            margin-left: 10px;
            vertical-align: middle;
        }
        
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        .top-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th {
            background: #f8fafc;
            padding: 16px;
            font-size: 12px;
            text-transform: uppercase;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        td {
            padding: 16px;
            border-bottom: 1px solid #f1f5f9;
            font-size: 14px;
            vertical-align: middle;
        }
        
        .id-badge {
            background: #e2e8f0;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 600;
            font-size: 12px;
            color: #1a73e8;
            display: inline-block;
        }

        .btn-add {
            background: #1a73e8;
            color: #fff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: background 0.2s;
        }

        .btn-add:hover {
            background: #155cb8;
        }
        
        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            color: #fff;
            text-decoration: none;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-action:hover {
            opacity: 0.85;
        }

        .bg-edit {
            background: #f59e0b;
            margin-right: 5px;
        }

        .bg-danger {
            background: #ef4444;
        }
        
        #searchInput {
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            width: 300px;
            outline: none;
            font-family: inherit;
        }

        #noDataRow td {
            text-align: center;
            color: #64748b;
            font-style: italic;
            padding: 30px;
        }

    </style>

</head>

<body>

<?php include '../layout/sidebar.php'; ?>

<div class="main-wrapper">

    <header class="top-header">

        <h3 style="margin: 0; font-size: 18px;">

            <i class="fa-solid fa-users" style="color: #1a73e8; margin-right: 10px;"></i>

            Data Anggota

        </h3>

        <div class="header-logos">

            <img src="<?= $base_url; ?>images/polijeee.png" alt="Logo Polije">

            <img src="<?= $base_url; ?>images/kpripolije.png" alt="Logo KPRI">

        </div>

    </header>

    <div class="table-container">

        <div class="top-action">

            <input 
                type="text"
                id="searchInput"
                onkeyup="liveSearch()"
                placeholder="Cari ID atau Nama Anggota..."
            >

            <?php if($role_saat_ini == 'admin'): ?>

                <a href="tambah.php" class="btn-add">

                    <i class="fa fa-plus"></i>

                    Tambah Anggota

                </a>

            <?php endif; ?>

        </div>

        <table>

            <thead>

                <tr>

                    <th style="width: 5%;">No</th>

                    <th style="width: 15%;">ID Anggota</th>

                    <th style="width: 10%;">Foto</th>

                    <th style="width: 25%;">Nama Lengkap</th>

                    <th style="width: 25%;">Alamat</th>

                    <th style="width: 10%;">WhatsApp</th>

                    <?php if($role_saat_ini == 'admin'): ?>

                        <th style="text-align:center; width: 10%;">

                            Aksi

                        </th>

                    <?php endif; ?>

                </tr>

            </thead>

            <tbody id="tableBody">

                <?php 
                $no = 1; 

                if (mysqli_num_rows($query) > 0):

                    while($row = mysqli_fetch_assoc($query)): 

                        // ✅ ERROR SUDAH DIBENERIN DI SINI
                        $foto_path = $base_url . "images/anggota/" . (!empty($row['foto']) ? $row['foto'] : 'default.png');
                ?>

                <tr class="data-row">

                    <td><?= $no++; ?></td>

                    <td class="id-data">

                        <span class="id-badge">

                            <?= htmlspecialchars($row['id_anggota']); ?>

                        </span>

                    </td>

                    <td>

                        <img 
                            src="<?= $foto_path; ?>"
                            style="width:40px; height:40px; border-radius:50%; object-fit:cover; cursor:pointer;"
                            onclick="bukaFoto('<?= htmlspecialchars($row['nama'], ENT_QUOTES); ?>', '<?= $foto_path; ?>')"
                            alt="Foto"
                        >

                    </td>

                    <td class="nama-data">

                        <strong><?= htmlspecialchars($row['nama']); ?></strong>

                    </td>

                    <td><?= htmlspecialchars($row['alamat']); ?></td>

                    <td><?= htmlspecialchars($row['no_hp']); ?></td>

                    <?php if($role_saat_ini == 'admin'): ?>

                    <td style="text-align:center; white-space: nowrap;">

                        <a 
                            href="edit.php?id=<?= $row['id_anggota']; ?>" 
                            class="btn-action bg-edit"
                            title="Edit"
                        >

                            <i class="fa fa-edit"></i>

                        </a>

                        <button 
                            class="btn-action bg-danger btn-hapus"
                            data-id="<?= $row['id_anggota']; ?>"
                            title="Hapus"
                        >

                            <i class="fa fa-trash"></i>

                        </button>

                    </td>

                    <?php endif; ?>

                </tr>

                <?php 
                    endwhile; 

                else: 
                ?>

                <tr id="noDataRow">

                    <td colspan="<?= ($role_saat_ini == 'admin') ? 7 : 6; ?>">

                        Belum ada data anggota.

                    </td>

                </tr>

                <?php endif; ?>
                    
                <tr id="searchEmptyRow" style="display: none;">

                    <td colspan="<?= ($role_saat_ini == 'admin') ? 7 : 6; ?>" 
                        style="text-align:center; color:#64748b; font-style:italic; padding:30px;">

                        Data anggota tidak ditemukan.

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

<script>

function liveSearch() {

    let input = document.getElementById("searchInput").value.toLowerCase();

    let rows = document.querySelectorAll(".data-row");

    let emptyRow = document.getElementById("searchEmptyRow");

    let hasVisibleRow = false;
    
    rows.forEach(row => {

        let idText = row.querySelector(".id-data").innerText.toLowerCase();

        let namaText = row.querySelector(".nama-data").innerText.toLowerCase();
        
        if (idText.includes(input) || namaText.includes(input)) {

            row.style.display = "";

            hasVisibleRow = true;

        } else {

            row.style.display = "none";
        }
    });

    if(emptyRow) {

        if (!hasVisibleRow && input !== "") {

            emptyRow.style.display = "";

        } else {

            emptyRow.style.display = "none";
        }
    }
}

document.querySelectorAll('.btn-hapus').forEach(button => {

    button.addEventListener('click', function() {

        const id = this.getAttribute('data-id');

        Swal.fire({

            title: 'Yakin hapus?',
            text: "Data akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'

        }).then((result) => {

            if (result.isConfirmed) {

                window.location.href = 'hapus.php?id=' + id;
            }
        });
    });
});

function bukaFoto(nama, url) {

    Swal.fire({ 

        title: nama,
        imageUrl: url,
        imageWidth: 300,
        imageHeight: 300,
        imageAlt: 'Foto ' + nama,
        confirmButtonColor: '#1a73e8'

    });
}

</script>

</body>
</html>