<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kerusakan_alat extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }

        $role = strtolower($this->session->userdata('role') ?? '');
        // Proteksi Ketat: Hanya Admin & Laboran yang boleh mengakses menu ini
        if (!in_array($role, array('admin', 'laboran'))) {
            $this->session->set_flashdata('error', 'Akses ditolak! Menu Kerusakan Alat hanya untuk Admin dan Laboran.');
            redirect('dashboard');
        }
    }

    // ==========================================
    // INDEX: DAFTAR LAPORAN KERUSAKAN ALAT
    // ==========================================
    public function index()
    {
        $keyword = trim($this->input->get('q', TRUE) ?? '');

        // PERBAIKAN ALIAS TABEL: u_peminjam (Tanpa Spasi)
        $this->db->select('k.*, p.kode_peminjaman, i.nama_barang, i.satuan, u_peminjam.nama_lengkap AS nama_peminjam, u_lab.nama_lengkap AS nama_laboran');
        $this->db->from('tb_kerusakan_alat k');
        $this->db->join('tb_peminjaman p', 'p.id_peminjaman = k.id_peminjaman', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = k.id_barang', 'left');
        $this->db->join('tb_users u_peminjam', 'u_peminjam.id_user = p.id_user', 'left');
        $this->db->join('tb_users u_lab', 'u_lab.id_user = k.id_laboran', 'left');

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.kode_peminjaman', $keyword);
            $this->db->or_like('i.nama_barang', $keyword);
            $this->db->or_like('u_peminjam.nama_lengkap', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('k.id_kerusakan', 'DESC');
        $data['kerusakan'] = $this->db->get()->result();
        $data['title']     = 'Laporan Kerusakan Alat';
        $data['keyword']   = $keyword;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('kerusakan_alat/index', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES UPDATE STATUS PERBAIKAN
    // ==========================================
    public function update_status()
    {
        if ($this->input->method() !== 'post') {
            show_error('Gunakan formulir status perbaikan.', 405);
            return;
        }

        $id = $this->input->post('id_kerusakan', TRUE);
        $status = $this->input->post('status_perbaikan', TRUE);
        $valid = ['Menunggu Perbaikan', 'Sedang Diperbaiki',
            'Selesai Diperbaiki', 'Afkir/Diganti'];
        $aktif_status = ['Menunggu Perbaikan', 'Sedang Diperbaiki'];

        if (!is_string($id) || !ctype_digit($id) || (int) $id <= 0
            || !in_array($status, $valid, TRUE)) {
            $this->session->set_flashdata('error', 'Status atau ID tidak valid.');
            redirect('kerusakan_alat');
            return;
        }

        $lap = $this->db->get_where('tb_kerusakan_alat', [
            'id_kerusakan' => (int) $id
        ])->row();

        if (!$lap || !in_array($lap->status_perbaikan, $aktif_status, TRUE)) {
            $this->session->set_flashdata('error', 'Laporan tidak ditemukan atau sudah selesai.');
            redirect('kerusakan_alat');
            return;
        }
        if ($lap->status_perbaikan === $status) {
            $this->session->set_flashdata('success', 'Status tetap; stok tidak berubah.');
            redirect('kerusakan_alat');
            return;
        }

        $this->load->model('M_Inventaris');
        $jumlah = (int) $lap->jumlah_rusak;
        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $berhasil = FALSE;

        if ($this->db->trans_start()) {
            $update = $this->db
                ->where('id_kerusakan', $lap->id_kerusakan)
                ->where('status_perbaikan', $lap->status_perbaikan)
                ->where('jumlah_rusak', $lap->jumlah_rusak)
                ->where('id_barang', $lap->id_barang)
                ->update('tb_kerusakan_alat', [
                    'status_perbaikan' => $status,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            $ok = $update && $this->db->affected_rows() === 1;

            if ($ok && $status === 'Selesai Diperbaiki' && $jumlah > 0) {
                $stok = $this->M_Inventaris->tambah_stok($lap->id_barang, $jumlah);
                $ok = $stok && $this->db->affected_rows() === 1;
            }

            if ($ok) {
                $berhasil = $this->db->trans_complete();
                if (!$berhasil) {
                    $this->db->trans_rollback();
                }
            } else {
                $this->db->trans_rollback();
            }
        }

        $this->db->db_debug = $debug;
        $pesan = $berhasil
            ? 'Status perbaikan berhasil diperbarui.'
            : 'Perubahan gagal atau data telah berubah. Muat ulang halaman.';
        if ($berhasil && $status === 'Selesai Diperbaiki' && $jumlah === 0) {
            $pesan .= ' Laporan lama: stok tidak ditambahkan; periksa pencatatan sebelumnya.';
        }
        $this->session->set_flashdata($berhasil ? 'success' : 'error', $pesan);
        redirect('kerusakan_alat');
    }

    // ==========================================
    // FORM FILTER EXPORT LAPORAN KERUSAKAN ALAT
    // ==========================================
    public function export_laporan()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Proteksi Role: Hanya Admin, Kepsek, dan Laboran
        if (!in_array($role_user, array('admin', 'kepsek', 'laboran'))) {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur cetak laporan hanya untuk Admin, Kepsek, dan Laboran.');
            redirect('kerusakan_alat');
            return;
        }

        $data['title'] = 'Export Laporan Kerusakan Alat Praktikum';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('kerusakan_alat/v_export_laporan', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES DOWNLOAD FILE EXCEL LAPORAN KERUSAKAN
    // ==========================================
    public function proses_export_excel()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        if (!in_array($role_user, array('admin', 'kepsek', 'laboran'))) {
            redirect('kerusakan_alat');
            return;
        }

        $tgl_mulai        = $this->input->get('tgl_mulai', TRUE);
        $tgl_selesai      = $this->input->get('tgl_selesai', TRUE);
        $status_perbaikan = $this->input->get('status_perbaikan', TRUE);

        // Kueri join disesuaikan dengan struktur tb_kerusakan_alat
        $this->db->select('k.*, u.nama_lengkap AS nama_pelapor, i.nama_barang');
        $this->db->from('tb_kerusakan_alat k');
        $this->db->join('tb_users u', 'u.id_user = k.id_laboran', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = k.id_barang', 'left');

        // Filter tanggal menggunakan kolom tgl_laporan
        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $this->db->where("DATE(k.tgl_laporan) >=", $tgl_mulai);
            $this->db->where("DATE(k.tgl_laporan) <=", $tgl_selesai);
        }

        // Filter status perbaikan
        if (!empty($status_perbaikan) && $status_perbaikan !== 'Semua') {
            $this->db->where('k.status_perbaikan', $status_perbaikan);
        }

        $this->db->order_by('k.id_kerusakan', 'DESC');
        $data['laporan'] = $this->db->get()->result();

        // Data Penandatangan (Kepsek & Laboran)
        $data['kepsek']  = $this->db->get_where('tb_users', array('role' => 'kepsek'))->row();
        $data['laboran'] = $this->db->get_where('tb_users', array('role' => 'laboran'))->row();

        $data['tgl_mulai']   = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        // Header download berkas Excel
        $filename = "Laporan_Kerusakan_Alat_Lab_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $this->load->view('kerusakan_alat/v_excel_laporan', $data);
    }
}
