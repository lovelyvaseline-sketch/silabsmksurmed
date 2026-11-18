<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Pengadaan extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // 1. Cek Login
        if (!$this->session->userdata('id_user') && !$this->session->userdata('logged_in')) {
            redirect('auth');
            return;
        }

        // 2. Proteksi Akses: Siswa DILARANG Mengakses Modul Pengadaan
        $role_user = strtolower($this->session->userdata('role') ?? '');
        if ($role_user === 'siswa') {
            $this->session->set_flashdata('error', 'Akses ditolak! Siswa tidak memiliki hak akses ke modul Pengadaan.');
            redirect('dashboard');
            return;
        }
    }

    // Tampilan Utama / Daftar Pengadaan
    public function index()
    {
        $role_user     = strtolower($this->session->userdata('role') ?? '');
        $id_user_login = $this->session->userdata('id_user');

        $keyword  = trim($this->input->get('q', TRUE) ?? '');
        $page     = (int) $this->input->get('page');
        $offset   = ($page > 0) ? $page : 0;
        $per_page = 10;

        // ---------------------------------------------------
        // 1. QUERY HITUNG TOTAL DATA (PAGINATION)
        // ---------------------------------------------------
        $this->db->from('tb_pengadaan p');
        $this->db->join('tb_users u', 'u.id_user = p.id_pengaju', 'left');

        // ISOLASI DATA: Jika BUKAN Admin, Laboran, atau Kepsek (misal: Guru)
        // Maka HANYA menampilkan data pengajuan miliknya sendiri
        if (!in_array($role_user, array('admin', 'laboran', 'kepsek'))) {
            $this->db->where('p.id_pengaju', $id_user_login);
        }

        // Filter Pencarian Keyword
        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.nama_item', $keyword);
            $this->db->or_like('p.alasan_pengadaan', $keyword);
            $this->db->or_like('u.nama_lengkap', $keyword);
            $this->db->group_end();
        }

        $total_rows = $this->db->count_all_results();

        // ---------------------------------------------------
        // 2. KONFIGURASI PAGINATION
        // ---------------------------------------------------
        $this->load->library('pagination');
        $config['base_url']             = base_url('pengadaan');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = $per_page;
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string']   = TRUE;

        $config['full_tag_open']   = '<ul class="inline-flex items-center gap-1 text-xs">';
        $config['full_tag_close']  = '</ul>';
        $config['cur_tag_open']    = '<li><span class="flex items-center justify-center px-3 h-8 text-white bg-teal-600 rounded-lg font-semibold shadow-sm">';
        $config['cur_tag_close']   = '</span></li>';
        $config['attributes']      = ['class' => 'flex items-center justify-center px-3 h-8 text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition'];

        $this->pagination->initialize($config);

        // ---------------------------------------------------
        // 3. QUERY UTAMA AMBIL DATA PENGADAAN
        // ---------------------------------------------------
        $this->db->select('p.*, u.nama_lengkap AS nama_pengaju');
        $this->db->from('tb_pengadaan p');
        $this->db->join('tb_users u', 'u.id_user = p.id_pengaju', 'left');

        // ISOLASI DATA: Filter berdasarkan ID User Pengaju
        if (!in_array($role_user, array('admin', 'laboran', 'kepsek'))) {
            $this->db->where('p.id_pengaju', $id_user_login);
        }

        // Filter Pencarian Keyword
        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.nama_item', $keyword);
            $this->db->or_like('p.alasan_pengadaan', $keyword);
            $this->db->or_like('u.nama_lengkap', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('p.id_pengadaan', 'DESC');
        $this->db->limit($per_page, $offset);

        $data['pengadaan']  = $this->db->get()->result();
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $total_rows;
        $data['offset']     = $offset;
        $data['keyword']    = $keyword;
        $data['title']      = 'Pengadaan Alat & BHP';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengadaan/index', $data);
        $this->load->view('templates/footer');
    }

    // Form Tambah Pengajuan
    public function tambah()
    {
        // Cek session login terlebih dahulu
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }

        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Blokir akses jika role Kepsek atau Siswa
        if (in_array($role_user, ['kepsek', 'siswa'])) {
            $this->session->set_flashdata('error', 'Akses ditolak! Role Anda tidak memiliki wewenang untuk mengajukan pengadaan.');
            redirect('pengadaan');
            return;
        }

        $data['title'] = 'Form Pengajuan Pengadaan Alat / BHP';


        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengadaan/tambah', $data);
        $this->load->view('templates/footer');
    }

    // Proses Simpan
    public function simpan()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');
        $id_pengaju = $this->session->userdata('id_user');

        if (in_array($role_user, ['kepsek', 'siswa'], TRUE) || empty($id_pengaju)) {
            $this->session->set_flashdata('error', 'Akses ditolak! Role Anda tidak dapat mengajukan pengadaan.');
            redirect('pengadaan');
            return;
        }

        // Fix Validasi nama barang kosong
        $this->load->library('form_validation');
        $this->form_validation->set_rules(
        'nama_item',
        'Nama barang',
        'trim|required'
        );

        if ($this->form_validation->run() === FALSE) {
            $this->session->set_flashdata('error', 'Nama barang wajib diisi.');
            redirect('pengadaan');
            return;
        }
        $data_insert = [
            'nama_item'        => $this->input->post('nama_item', true),
            'jumlah'           => (int)$this->input->post('jumlah', true),
            'satuan'           => $this->input->post('satuan', true),
            'estimasi_harga'   => (float)$this->input->post('estimasi_harga', true),
            'alasan_pengadaan' => $this->input->post('alasan_pengadaan', true),
            'id_pengaju'       => $id_pengaju,
            'status_approval'  => 'Pending',
            'created_at'       => date('Y-m-d H:i:s')
        ];

        $this->db->insert('tb_pengadaan', $data_insert);
        $this->session->set_flashdata('success', 'Usulan pengadaan berhasil diajukan!');
        redirect('pengadaan');
    }

    // Tampilan Detail
    public function detail($id_pengadaan = NULL)
    {
        if (empty($id_pengadaan)) {
            redirect('pengadaan');
            return;
        }

        // Fetch data join ke tb_users untuk nama pengaju & role
        $this->db->select('p.*, u.nama_lengkap AS nama_pengaju, u.role AS role_pengaju');
        $this->db->from('tb_pengadaan p');
        $this->db->join('tb_users u', 'u.id_user = p.id_pengaju', 'left');
        $this->db->where('p.id_pengadaan', $id_pengadaan);
        $role_user = strtolower($this->session->userdata('role') ?? '');
        $id_user_login = $this->session->userdata('id_user');
        $pengadaan = $this->db->get()->row();

        if (!$pengadaan) {
            $this->session->set_flashdata('error', 'Data pengadaan tidak ditemukan.');
            redirect('pengadaan');
            return;
        }

        if (!in_array($role_user, ['admin', 'laboran', 'kepsek'], TRUE)
            && (int) $pengadaan->id_pengaju !== (int) $id_user_login) {
            $this->session->set_flashdata('error', 'Akses ditolak! Pengadaan bukan milik Anda.');
            redirect('pengadaan');
            return;
        }

        $data['title']     = 'Detail Pengadaan Alat & BHP';
        $data['pengadaan'] = $pengadaan;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengadaan/detail', $data);
        $this->load->view('templates/footer');
    }

    // Update Status Approval
    public function update_status($id_pengadaan = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        $status_baru = $this->input->post('status_baru', TRUE);

        if (empty($id_pengadaan) || empty($status_baru)) {
            redirect('pengadaan');
            return;
        }

        $role_user   = strtolower($this->session->userdata('role') ?? '');
        $status_valid = ['Disetujui Laboran', 'Ditolak Laboran',
            'Disetujui Kepsek', 'Ditolak Kepsek', 'Selesai'];
        if (!in_array($status_baru, $status_valid, TRUE)) {
            $this->session->set_flashdata('error', 'Status approval tidak valid.');
            redirect('pengadaan');
            return;
        }

        // Ambil data pengadaan berdasarkan ID
        $pengadaan = $this->db->get_where('tb_pengadaan', array('id_pengadaan' => $id_pengadaan))->row();

        if (!$pengadaan) {
            $this->session->set_flashdata('error', 'Data pengadaan tidak ditemukan.');
            redirect('pengadaan');
            return;
        }

        $status_saat_ini = $pengadaan->status_approval ?? 'Pending';

        // ----------------------------------------------------
        // TAHAP 1: VERIFIKASI LABORAN (Pengecekan Fisik & Kebutuhan)
        // ----------------------------------------------------
        if (in_array($status_baru, array('Disetujui Laboran', 'Ditolak Laboran'))) {
            if (!in_array($role_user, array('laboran', 'admin'))) {
                $this->session->set_flashdata('error', 'Akses ditolak! Verifikasi tahap 1 hanya dapat dilakukan oleh Laboran.');
                redirect('pengadaan');
                return;
            }
            if ($status_saat_ini !== 'Pending') {
                $this->session->set_flashdata('error', 'Pengajuan hanya dapat diverifikasi Laboran dari status Pending.');
                redirect('pengadaan');
                return;
            }

            $update_status = ($status_baru === 'Ditolak Laboran') ? 'Ditolak' : 'Disetujui Laboran';
            $pesan_sukses   = ($update_status === 'Ditolak')
                ? 'Pengajuan pengadaan ditolak oleh Laboran.'
                : 'Pengajuan berhasil diverifikasi Laboran dan meneruskan ke Kepala Sekolah.';
        }

        // ----------------------------------------------------
        // TAHAP 2: ACC KEPALA SEKOLAH (Persetujuan Anggaran)
        // ----------------------------------------------------
        elseif (in_array($status_baru, array('Disetujui Kepsek', 'Ditolak Kepsek'))) {
            if (!in_array($role_user, array('kepsek', 'admin'))) {
                $this->session->set_flashdata('error', 'Akses ditolak! ACC pengadaan hanya dapat dilakukan oleh Kepala Sekolah.');
                redirect('pengadaan');
                return;
            }

            // Memastikan pengajuan sudah diverifikasi oleh Laboran terlebih dahulu
            if ($status_saat_ini !== 'Disetujui Laboran') {
                $this->session->set_flashdata('error', 'Gagal! Pengajuan harus diverifikasi oleh Laboran terlebih dahulu.');
                redirect('pengadaan');
                return;
            }

            $update_status = ($status_baru === 'Ditolak Kepsek') ? 'Ditolak' : 'Disetujui Kepsek';
            $pesan_sukses   = ($update_status === 'Ditolak')
                ? 'Pengajuan pengadaan ditolak oleh Kepala Sekolah.'
                : 'Pengajuan pengadaan berhasil di-ACC oleh Kepala Sekolah!';
        }

        // ----------------------------------------------------
        // TAHAP 3: BARANG DITERIMA (PENYELESAIAN PENGADAAN)
        // ----------------------------------------------------
        elseif ($status_baru === 'Selesai') {
            if (!in_array($role_user, array('admin', 'laboran'))) {
                $this->session->set_flashdata('error', 'Akses ditolak!');
                redirect('pengadaan');
                return;
            }
            if ($status_saat_ini !== 'Disetujui Kepsek') {
                $this->session->set_flashdata('error', 'Barang hanya dapat ditandai selesai setelah disetujui Kepala Sekolah.');
                redirect('pengadaan');
                return;
            }

            $update_status = 'Selesai';
            $pesan_sukses   = 'Pengadaan barang telah selesai dan ditandai Diterima.';
        }

        // Eksekusi Update ke database pada kolom status_approval
        $this->db->where('id_pengadaan', $id_pengadaan);
        $this->db->where('status_approval', $status_saat_ini);
        $updated = $this->db->update('tb_pengadaan', array(
            'status_approval' => $update_status
        ));

        if (!$updated || $this->db->affected_rows() !== 1) {
            $this->session->set_flashdata('error', 'Status pengadaan berubah atau gagal diperbarui.');
            redirect('pengadaan');
            return;
        }

        $this->session->set_flashdata('success', $pesan_sukses);
        redirect('pengadaan');
    }


    // ==========================================
    // PROSES DOWNLOAD FILE EXCEL LAPORAN
    // ==========================================
    public function proses_export_excel()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        if (!in_array($role_user, array('admin', 'kepsek', 'laboran'))) {
            redirect('pengadaan');
            return;
        }

        $tgl_mulai   = $this->input->get('tgl_mulai', TRUE);
        $tgl_selesai = $this->input->get('tgl_selesai', TRUE);
        $status      = $this->input->get('status', TRUE);

        // Fetch data pengadaan join dengan user pengaju
        $this->db->select('p.*, u.nama_lengkap AS nama_pengaju');
        $this->db->from('tb_pengadaan p');
        $this->db->join('tb_users u', 'u.id_user = p.id_pengaju', 'left');

        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $this->db->where("DATE(p.created_at) >=", $tgl_mulai);
            $this->db->where("DATE(p.created_at) <=", $tgl_selesai);
        }

        if (!empty($status) && $status !== 'Semua') {
            $this->db->where('p.status_approval', $status);
        }

        $this->db->order_by('p.id_pengadaan', 'DESC');
        $data['laporan'] = $this->db->get()->result();

        // Cari Nama Kepala Sekolah & Laboran untuk kolom Tanda Tangan
        $data['kepsek']  = $this->db->get_where('tb_users', array('role' => 'kepsek'))->row();
        $data['laboran'] = $this->db->get_where('tb_users', array('role' => 'laboran'))->row();

        $data['tgl_mulai']   = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        // Set Header untuk Download File Excel
        $filename = "Laporan_Pengadaan_Alat_BHP_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $this->load->view('pengadaan/v_excel_laporan', $data);
    }

    // ==========================================
    // FORM EDIT / APPROVAL PENGADAAN
    // ==========================================
    public function edit($id_pengadaan = NULL)
    {
        if (empty($id_pengadaan)) {
            redirect('pengadaan');
            return;
        }

        $role_user     = strtolower($this->session->userdata('role') ?? '');
        $id_user_login = $this->session->userdata('id_user');

        if ($role_user !== 'admin' && $role_user !== 'guru') {
            $this->session->set_flashdata('error', 'Akses ditolak! Role Anda tidak dapat mengedit pengadaan.');
            redirect('pengadaan');
            return;
        }

        // Ambil data pengadaan join dengan tb_users
        $this->db->select('p.*, u.nama_lengkap AS nama_pengaju');
        $this->db->from('tb_pengadaan p');
        $this->db->join('tb_users u', 'u.id_user = p.id_pengaju', 'left');
        $this->db->where('p.id_pengadaan', $id_pengadaan);
        $pengadaan = $this->db->get()->row();

        if (!$pengadaan) {
            $this->session->set_flashdata('error', 'Data pengadaan tidak ditemukan.');
            redirect('pengadaan');
            return;
        }

        $status_saat_ini = $pengadaan->status_approval ?? 'Pending';

        // PROTEKSI 1: Hanya status PENDING yang boleh diedit
        if ($status_saat_ini !== 'Pending' && $role_user !== 'admin') {
            $this->session->set_flashdata('error', 'Gagal! Pengajuan yang sudah diproses atau disetujui tidak dapat diedit kembali.');
            redirect('pengadaan');
            return;
        }

        // Hanya pengaju Guru pada status Pending atau Admin yang boleh mengedit.
        if ($role_user !== 'admin'
            && !($role_user === 'guru'
                && (int) $pengadaan->id_pengaju === (int) $id_user_login
                && $status_saat_ini === 'Pending')) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda hanya dapat mengedit pengajuan milik Anda sendiri.');
            redirect('pengadaan');
            return;
        }

        $data['title']     = 'Edit Pengajuan Pengadaan Alat & BHP';
        $data['pengadaan'] = $pengadaan;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('pengadaan/edit', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES SIMPAN EDIT PENGAJUAN
    // ==========================================
    public function proses_edit()
    {
        $id_pengadaan  = $this->input->post('id_pengadaan', TRUE);
        $role_user     = strtolower($this->session->userdata('role') ?? '');
        $id_user_login = $this->session->userdata('id_user');

        if ($role_user !== 'admin' && $role_user !== 'guru') {
            $this->session->set_flashdata('error', 'Akses ditolak! Role Anda tidak dapat mengedit pengadaan.');
            redirect('pengadaan');
            return;
        }

        $pengadaan = $this->db->get_where('tb_pengadaan', array('id_pengadaan' => $id_pengadaan))->row();

        if (!$pengadaan) {
            redirect('pengadaan');
            return;
        }

        // Proteksi Ulang di Server Side
        if (($pengadaan->status_approval ?? 'Pending') !== 'Pending' && $role_user !== 'admin') {
            $this->session->set_flashdata('error', 'Data tidak dapat diubah karena sudah masuk proses approval.');
            redirect('pengadaan');
            return;
        }

        $data_update = array(
            'nama_item'        => $this->input->post('nama_item', TRUE),
            'jumlah'           => $this->input->post('jumlah', TRUE),
            'satuan'           => $this->input->post('satuan', TRUE),
            'estimasi_harga'   => $this->input->post('estimasi_harga', TRUE),
            'alasan_pengadaan' => $this->input->post('alasan_pengadaan', TRUE),
        );

        $this->db->where('id_pengadaan', $id_pengadaan);
        if ($role_user !== 'admin') {
            $this->db->where('id_pengaju', $id_user_login);
            $this->db->where('status_approval', 'Pending');
        }
        $updated = $this->db->update('tb_pengadaan', $data_update);

        if (!$updated || $this->db->affected_rows() !== 1) {
            $this->session->set_flashdata('error', 'Data berubah atau gagal diperbarui.');
            redirect('pengadaan');
            return;
        }
        $this->session->set_flashdata('success', 'Rincian pengajuan pengadaan berhasil diperbarui.');
        redirect('pengadaan');
    }

    // ==========================================
    // HAPUS DATA PENGADAAN ALAT & BHP
    // ==========================================
    // ==========================================
    // HAPUS DATA PENGADAAN (KHUSUS ADMIN & KEPSEK)
    // ==========================================
    public function delete($id_pengadaan = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        if (empty($id_pengadaan)) {
            redirect('pengadaan');
            return;
        }

        $role_user = strtolower($this->session->userdata('role') ?? '');

        // PROTEKSI ROLE: Hanya Admin dan Kepala Sekolah yang diizinkan menghapus data
        if (!in_array($role_user, array('admin', 'kepsek'))) {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur hapus pengadaan hanya dapat dilakukan oleh Admin dan Kepala Sekolah.');
            redirect('pengadaan');
            return;
        }

        // Cari data pengadaan berdasarkan ID
        $pengadaan = $this->db->get_where('tb_pengadaan', array('id_pengadaan' => $id_pengadaan))->row();

        if (!$pengadaan) {
            $this->session->set_flashdata('error', 'Data pengadaan tidak ditemukan.');
            redirect('pengadaan');
            return;
        }

        // Eksekusi Hapus Data dari database
        $this->db->where('id_pengadaan', $id_pengadaan);
        $delete = $this->db->delete('tb_pengadaan');

        if ($delete) {
            $this->session->set_flashdata('success', 'Data pengajuan pengadaan berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', 'Gagal menghapus data pengadaan.');
        }

        redirect('pengadaan');
    }
}
