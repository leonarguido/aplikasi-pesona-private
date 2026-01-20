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

        if (isset($_POST['tambah'])) {
            $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $username = mysqli_real_escape_string($koneksi, $_POST['username']);
            $role     = $_POST['role'];
            $nip      = $_POST['nip'];
            // Password default user baru: '123456' (bisa diganti user nanti)
            $password = password_hash("123456", PASSWORD_DEFAULT);

            // Cek Username Kembar
            $cek = mysqli_query($koneksi, "SELECT * FROM tb_user WHERE username = '$username'");
            if (mysqli_num_rows($cek) > 0) {
                echo "<script>alert('Username sudah digunakan!');</script>";
            } else {
                $query = "INSERT INTO tb_user (nama, nip, username, password, role) VALUES ('$nama', '$nip', '$username', '$password', '$role')";
                if (mysqli_query($koneksi, $query)) {
                    echo "<script>alert('User berhasil ditambahkan! Password default: 123456'); window.location='" . $this->base_url . "data_pengguna';</script>";
                } else {
                    echo "<script>alert('Gagal: " . mysqli_error($koneksi) . "');</script>";
                }
            }
        }
    }

    // B. PROSES EDIT USER
    public function edit_data_pengguna()
    {
        require __DIR__ . '/../config/koneksi.php';

        if (isset($_POST['edit'])) {
            $id       = $_POST['id'];
            $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
            $role     = $_POST['role'];
            $nip      = $_POST['nip'];

            // Jika password diisi, update password. Jika kosong, biarkan password lama.
            if (!empty($_POST['password'])) {
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $query = "UPDATE tb_user SET nama='$nama', nip='$nip', role='$role', password='$password' WHERE id='$id'";
            } else {
                $query = "UPDATE tb_user SET nama='$nama', nip='$nip', role='$role' WHERE id='$id'";
            }

            if (mysqli_query($koneksi, $query)) {
                echo "<script>alert('Data pengguna berhasil diupdate!'); window.location='" . $this->base_url . "data_pengguna';</script>";
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
            // Cegah hapus diri sendiri
            if ($id == $_SESSION['user_id']) {
                echo "<script>alert('Anda tidak bisa menghapus akun sendiri!'); window.location='" . $this->base_url . "data_pengguna';</script>";
            } else {
                $query = "DELETE FROM tb_user WHERE id = '$id'";
                if (mysqli_query($koneksi, $query)) {
                    echo "<script>alert('User berhasil dihapus!'); window.location='" . $this->base_url . "data_pengguna';</script>";
                }
            }
        }
    }

    public function profil_page()
    {
        require_once '../views/profil.php';
    }
}
