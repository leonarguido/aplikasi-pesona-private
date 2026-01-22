<?php

class UserController
{
    protected $base_url = '/aplikasi-pesona-private/routes/web.php/?method=';

    public function data_pengguna_page()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        // 1. Cek Login & Akses
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        // Hanya Super Admin yang boleh akses halaman ini
        if ($_SESSION['role'] != 'super_admin') {
            echo "<script>alert('Akses Ditolak!'); window.location='index.php';</script>";
            exit;
        }

        require_once '../views/data_pengguna.php';
    }

    // A. PROSES TAMBAH USER
    public function tambah_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['tambah_user'])) {
            $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
            $username = mysqli_real_escape_string($koneksi, $_POST['username']);
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $role     = $_POST['role']; // Ambil value dari select option

            // Upload Tanda Tangan
            $paraf_name = null;
            if (!empty($_FILES['paraf']['name'])) {
                $filename   = $_FILES['paraf']['name'];
                $filesize   = $_FILES['paraf']['size'];
                $ext        = pathinfo($filename, PATHINFO_EXTENSION);
                $allowed    = ['png', 'jpg', 'jpeg'];

                if (!in_array(strtolower($ext), $allowed)) {
                    echo "<script>alert('Format TTD harus PNG, JPG, atau JPEG!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                    exit;
                }
                if ($filesize > 2000000) {
                    echo "<script>alert('Ukuran file TTD terlalu besar (Max 2MB)!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                    exit;
                }

                $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['paraf']['tmp_name'], 'assets/img/ttd/' . $paraf_name);
            }

            $q = "INSERT INTO tb_user (nama, nip, username, password, role, paraf) 
          VALUES ('$nama', '$nip', '$username', '$password', '$role', '$paraf_name')";

            if (mysqli_query($koneksi, $q)) {
                echo "<script>alert('User Berhasil Ditambahkan!'); window.location='" . $this->base_url . "data_pengguna';</script>";
            } else {
                echo "<script>alert('Gagal menambah user: " . mysqli_error($koneksi) . "');</script>";
            }
        }
    }

    // B. PROSES EDIT USER
    public function edit_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';
        // var_dump($_POST);
        // exit;

        if (isset($_POST['edit_user'])) {
            $id       = $_POST['id_user'];
            $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $nip      = mysqli_real_escape_string($koneksi, $_POST['nip']);
            $username = mysqli_real_escape_string($koneksi, $_POST['username']);
            $role     = $_POST['role'];

            // Password Logic
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $q_pass = ", password='$password'";
            } else {
                $q_pass = "";
            }

            // TTD Logic
            $q_ttd = "";
            if (!empty($_FILES['paraf']['name'])) {
                $filename   = $_FILES['paraf']['name'];
                $ext        = pathinfo($filename, PATHINFO_EXTENSION);
                $paraf_name = date('Ymd_His') . '_' . uniqid() . '.' . $ext;
                move_uploaded_file($_FILES['paraf']['tmp_name'], 'assets/img/ttd/' . $paraf_name);
                $q_ttd = ", paraf='$paraf_name'";
            }

            $q = "UPDATE tb_user SET 
          nama='$nama', nip='$nip', username='$username', role='$role' $q_pass $q_ttd
          WHERE id='$id'";

            if (mysqli_query($koneksi, $q)) {
                echo "<script>alert('Data User Diupdate!'); window.location='" . $this->base_url . "data_pengguna';</script>";
            } else {
                echo "<script>alert('Gagal update!');</script>";
            }
        }
    }

    // C. PROSES HAPUS USER
    public function hapus_data_pengguna()
    {
        session_start();
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_GET['hapus'])) {
            $id = $_GET['hapus'];
            $q_img = mysqli_query($koneksi, "SELECT paraf FROM tb_user WHERE id='$id'");
            $row_img = mysqli_fetch_assoc($q_img);
            if ($row_img['paraf'] != null && file_exists('assets/img/ttd/' . $row_img['paraf'])) {
                unlink('assets/img/ttd/' . $row_img['paraf']);
            }

            $q = "DELETE FROM tb_user WHERE id='$id'";
            if (mysqli_query($koneksi, $q)) {
                echo "<script>alert('User Dihapus!'); window.location='" . $this->base_url . "data_pengguna';</script>";
            } else {
                echo "<script>alert('Gagal hapus!');</script>";
            }
        }
    }

    public function profil_page()
    {
        require_once '../views/profil.php';
    }
}
