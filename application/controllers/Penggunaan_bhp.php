<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Penggunaan_bhp extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
    }

    // ==========================================
    // INDEX: DAFTAR PENGGUNAAN BHP
    // ==========================================
    public function index()
    {
        $role_user     = strtolower($this->session->userdata('role') ?? '');
        $id_user_login = $this->session->userdata('id_user');

        // Tangkap parameter query (search & pagination limit)
        $keyword  = trim($this->input->get('q', TRUE) ?? '');
        $limit    = (int) ($this->input->get('limit', TRUE) ?: 10);
        $page     = (int) ($this->input->get('page', TRUE) ?: 0);

        // Load Library Pagination
        $this->load->library('pagination');

        // Base Query untuk Menghitung Total Data
        $this->db->from('tb_penggunaan_bhp p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = p.id_barang', 'left');

        // Filter Hak Akses (Guru & Siswa hanya melihat data milik sendiri)
        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('p.id_user', $id_user_login);
        }

        // Filter Pencarian
        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.kode_penggunaan', $keyword);
            $this->db->or_like('i.nama_barang', $keyword);
            $this->db->or_like('u.nama_lengkap', $keyword);
            $this->db->group_end();
        }

        $total_rows = $this->db->count_all_results();

        // Konfigurasi CodeIgniter Pagination
        $config['base_url']             = site_url('penggunaan_bhp');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = $limit;
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string']   = TRUE;

        // Custom Styling Styling Tailwind CSS untuk Pagination
        $config['full_tag_open']   = '<div class="flex items-center gap-1.5">';
        $config['full_tag_close']  = '</div>';
        $config['cur_tag_open']   = '<span class="px-3 py-1.5 text-xs font-bold text-white bg-teal-600 rounded-lg shadow-sm">';
        $config['cur_tag_close']  = '</span>';
        $config['num_tag_open']   = '<span class="text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition py-1 px-1.5">';
        $config['num_tag_close']  = '</span>';
        $config['next_tag_open']  = '<span class="text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition py-1 px-1.5">';
        $config['next_tag_close'] = '</span>';
        $config['prev_tag_open']  = '<span class="text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-lg transition py-1 px-1.5">';
        $config['prev_tag_close'] = '</span>';

        $this->pagination->initialize($config);

        // Fetch Data Sesuai Offset Limit
        $this->db->select('p.*, u.nama_lengkap AS pemakai, i.nama_barang, i.satuan');
        $this->db->from('tb_penggunaan_bhp p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = p.id_barang', 'left');

        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('p.id_user', $id_user_login);
        }

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.kode_penggunaan', $keyword);
            $this->db->or_like('i.nama_barang', $keyword);
            $this->db->or_like('u.nama_lengkap', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('p.id_penggunaan', 'DESC');
        $this->db->limit($limit, $page);
        $penggunaan = $this->db->get()->result();

        // Kirim Data ke View
        $data['title']      = 'Penggunaan Bahan Habis Pakai (BHP)';
        $data['penggunaan'] = $penggunaan;
        $data['keyword']    = $keyword;
        $data['per_page']   = $limit;
        $data['offset']     = $page;
        $data['total_rows'] = $total_rows;
        $data['pagination'] = $this->pagination->create_links();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('penggunaan_bhp/index', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // FORM AJUKAN PENGGUNAAN BHP
    // ==========================================
    public function tambah()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        if ($role_user === 'kepsek') {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('penggunaan_bhp');
            return;
        }

        $data['title'] = 'Form Penggunaan BHP';

        $data['bhp_list'] = $this->db->get_where('tb_inventaris', [
            'jenis' => 'Bahan',
            'kondisi' => 'Baik',
            'stok >' => 0
        ])->result();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('penggunaan_bhp/v_tambah', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES SIMPAN PENGAJUAN
    // ==========================================
    public function simpan()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Proteksi Kepsek
        if (!in_array($role_user, ['admin', 'laboran', 'guru', 'siswa'], TRUE)) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('penggunaan_bhp');
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id_barang', 'Barang', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('jumlah_pakai', 'Jumlah', 'required|integer|greater_than[0]');
        $this->form_validation->set_message('integer', '{field} harus berupa bilangan bulat.');
        $this->form_validation->set_message('greater_than', '{field} harus lebih dari nol.');

        if ($this->form_validation->run() == FALSE) {
            $this->tambah();
            return;
        }

        $id_barang    = $this->input->post('id_barang', TRUE);
        $jumlah_pakai = (int) $this->input->post('jumlah_pakai', TRUE);

        // 1. Validasi input barang
        if (empty($id_barang)) {
            $this->session->set_flashdata('error', 'Gagal! Silakan pilih bahan (BHP) terlebih dahulu.');
            redirect('penggunaan_bhp/tambah');
            return;
        }

        // 2. Validasi stok ketersediaan di tb_inventaris
        $barang = $this->db->get_where('tb_inventaris', [
            'id_barang' => $id_barang,
            'jenis' => 'Bahan',
            'kondisi' => 'Baik'
        ])->row();

        if (!$barang) {
            $this->session->set_flashdata('error', 'Pilih bahan yang tersedia dan berkondisi Baik.');
            redirect('penggunaan_bhp/tambah');
            return;
        }
        if (!$barang || $barang->stok < $jumlah_pakai) {
            $this->session->set_flashdata('error', 'Gagal! Stok bahan tidak mencukupi (Tersedia: ' . ($barang->stok ?? 0) . ').');
            redirect('penggunaan_bhp/tambah');
            return;
        }

        // Status Awal (Admin/Laboran langsung Disetujui, Guru/Siswa Pending)
        $status_awal = in_array($role_user, ['admin', 'laboran']) ? 'Disetujui' : 'Pending';

        // 3. Menyiapkan array data khusus untuk tb_penggunaan_bhp
        $data_insert = [
            'kode_penggunaan' => $this->input->post('kode_penggunaan', TRUE) ?: ('BHP-' . date('Ymd') . '-' . rand(100, 999)),
            'id_user'         => $this->session->userdata('id_user'),
            'id_barang'       => $id_barang,
            'jumlah_pakai'    => $jumlah_pakai,
            'tgl_penggunaan'  => $this->input->post('tgl_penggunaan', TRUE) ?: date('Y-m-d'),
            'keterangan'      => $this->input->post('keterangan', TRUE) ?: '-',
            'status'          => $status_awal,
            'created_at'      => date('Y-m-d H:i:s')
        ];

        $this->load->model('M_Inventaris');
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $berhasil = FALSE;

        try {
            if (!$this->db->trans_begin()) {
                throw new RuntimeException('Gagal memulai transaksi.');
            }

            $insert = $this->db->insert('tb_penggunaan_bhp', $data_insert);
            $stok = TRUE;
            if ($insert && $status_awal === 'Disetujui') {
                $stok = $this->M_Inventaris->kurangi_stok($id_barang, $jumlah_pakai, 'Bahan');
            }

            if ($insert && $stok && $this->db->trans_status() === TRUE) {
                $berhasil = $this->db->trans_commit();
            }
            if (!$berhasil) {
                $this->db->trans_rollback();
            }
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Gagal menyimpan penggunaan BHP: ' . $e->getMessage());
        } finally {
            $this->db->db_debug = $db_debug;
        }

        if ($berhasil) {
            $pesan = ($status_awal === 'Disetujui')
                ? 'Penggunaan BHP berhasil dicatat dan stok telah berkurang.'
                : 'Pengajuan penggunaan BHP berhasil dibuat, menunggu persetujuan Laboran.';
            $this->session->set_flashdata('success', $pesan);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan transaksi. Tidak ada perubahan stok yang disimpan.');
        }

        redirect('penggunaan_bhp');
    }


    // FORM CEK KONDISI PENGEMBALIAN (KHUSUS LABORAN/ADMIN)
    public function konfirmasi_kembali($id_peminjaman = NULL)
    {
        redirect('peminjaman/konfirmasi_kembali/' . (int) $id_peminjaman);
    }

    // PROSES PENGEMBALIAN & OTOMATIS CATAT LAPORAN KERUSAKAN JIKA ADA
    public function proses_pengembalian()
    {
        if ($this->input->method() !== 'post') {
            show_error('Gunakan formulir pengembalian.', 405);
            return;
        }

        $role = strtolower($this->session->userdata('role') ?? '');
        if (!in_array($role, ['admin', 'laboran'], TRUE)) {
            $this->session->set_flashdata('error', 'Akses ditolak.');
            redirect('peminjaman');
            return;
        }

        $id = $this->input->post('id_peminjaman', TRUE);
        $kondisi = $this->input->post('kondisi_sesudah', TRUE);
        $catatan = $this->input->post('catatan_laboran', TRUE) ?? '';
        if (!is_string($id) || !ctype_digit($id) || (int) $id <= 0
            || !in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'], TRUE)
            || !is_string($catatan)) {
            $this->session->set_flashdata('error', 'Data pengembalian tidak valid.');
            redirect('peminjaman');
            return;
        }

        $this->load->model('M_Peminjaman');
        $hasil = $this->M_Peminjaman->kembalikan_aman(
            (int) $id, $kondisi, trim($catatan),
            (int) $this->session->userdata('id_user')
        );
        if ($hasil === TRUE) {
            $this->session->set_flashdata('success', 'Pengembalian berhasil diproses.');
        } elseif ($hasil === 'sudah_diproses') {
            $this->session->set_flashdata('error', 'Peminjaman sudah diproses atau tidak sedang dipinjam.');
        } else {
            $this->session->set_flashdata('error', 'Pengembalian gagal. Tidak ada perubahan yang disimpan.');
        }
        redirect('peminjaman');
    }

    // ==========================================
    // APPROVAL PENGGUNAAN BHP (LABORAN & ADMIN)
    // ==========================================
    public function update_status($id_penggunaan = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        $status_baru = $this->input->post('status_baru', TRUE);

        $role_user = strtolower($this->session->userdata('role') ?? '');
        if (!in_array($role_user, ['admin', 'laboran'])) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak memiliki hak akses persetujuan.');
            redirect('penggunaan_bhp');
            return;
        }

        $status_decode = $status_baru;
        if (!in_array($status_decode, ['Disetujui', 'Ditolak'], TRUE)) {
            $this->session->set_flashdata('error', 'Perubahan status tidak sah.');
            redirect('penggunaan_bhp');
            return;
        }

        $penggunaan = $this->db->get_where('tb_penggunaan_bhp', ['id_penggunaan' => $id_penggunaan])->row();
        if (!$penggunaan || $penggunaan->status !== 'Pending') {
            $this->session->set_flashdata('error', 'Transaksi tidak ditemukan atau sudah diproses.');
            redirect('penggunaan_bhp');
            return;
        }

        $jumlah_pakai = (int) $penggunaan->jumlah_pakai;
        if ($jumlah_pakai <= 0) {
            $this->session->set_flashdata('error', 'Jumlah pada pengajuan tidak sah.');
            redirect('penggunaan_bhp');
            return;
        }

        $this->load->model('M_Inventaris');
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $berhasil = FALSE;

        try {
            if (!$this->db->trans_begin()) {
                throw new RuntimeException('Gagal memulai transaksi.');
            }

            $this->db->where('id_penggunaan', $id_penggunaan);
            $this->db->where('status', 'Pending');
            $this->db->where('id_barang', $penggunaan->id_barang);
            $this->db->where('jumlah_pakai', $jumlah_pakai);
            $update = $this->db->update('tb_penggunaan_bhp', [
                'status' => $status_decode,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            $berubah = $update && $this->db->affected_rows() === 1;

            $stok = TRUE;
            if ($berubah && $status_decode === 'Disetujui') {
                $stok = $this->M_Inventaris->kurangi_stok($penggunaan->id_barang, $jumlah_pakai, 'Bahan');
            }
            if ($berubah && $stok && $this->db->trans_status() === TRUE) {
                $berhasil = $this->db->trans_commit();
            }
            if (!$berhasil) {
                $this->db->trans_rollback();
            }
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Gagal memproses persetujuan BHP: ' . $e->getMessage());
        } finally {
            $this->db->db_debug = $db_debug;
        }

        if ($berhasil) {
            $this->session->set_flashdata('success', 'Pengajuan penggunaan BHP berhasil ' . strtolower($status_decode) . '.');
        } else {
            $this->session->set_flashdata('error', 'Pengajuan gagal diproses. Data berubah, stok tidak mencukupi, atau penyimpanan gagal.');
        }
        redirect('penggunaan_bhp');
    }

    // ==========================================
    // DETAIL PENGGUNAAN BHP
    // ==========================================
    public function detail($id_penggunaan = NULL)
    {
        if (empty($id_penggunaan)) {
            redirect('penggunaan_bhp');
            return;
        }

        $this->db->select('p.*, u.nama_lengkap AS pemakai, i.nama_barang, i.satuan');
        $this->db->from('tb_penggunaan_bhp p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = p.id_barang', 'left');
        $this->db->where('p.id_penggunaan', $id_penggunaan);

        $role_user = strtolower($this->session->userdata('role') ?? '');
        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('p.id_user', $this->session->userdata('id_user'));
        }
        $penggunaan = $this->db->get()->row();

        if (!$penggunaan) {
            $this->session->set_flashdata('error', 'Data penggunaan BHP tidak ditemukan.');
            redirect('penggunaan_bhp');
            return;
        }

        $data['title']      = 'Detail Penggunaan BHP';
        $data['penggunaan'] = $penggunaan;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('penggunaan_bhp/detail', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // FORM EDIT PENGGUNAAN BHP
    // ==========================================
    public function edit($id_penggunaan = NULL)
    {
        if (empty($id_penggunaan)) {
            redirect('penggunaan_bhp');
            return;
        }

        $role_user = strtolower($this->session->userdata('role') ?? '');
        $id_user_sess = $this->session->userdata('id_user');
        if (!in_array($role_user, ['admin', 'laboran', 'guru', 'siswa'])) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('penggunaan_bhp');
            return;
        }

        $penggunaan = $this->db->get_where('tb_penggunaan_bhp', ['id_penggunaan' => $id_penggunaan])->row();
        if (!$penggunaan) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan.');
            redirect('penggunaan_bhp');
            return;
        }

        if (in_array($role_user, ['guru', 'siswa']) && $penggunaan->id_user != $id_user_sess) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda hanya dapat menyunting pengajuan sendiri.');
            redirect('penggunaan_bhp');
            return;
        }

        if ($penggunaan->status !== 'Pending') {
            $this->session->set_flashdata('error', 'Hanya pengajuan berstatus Pending yang dapat disunting.');
            redirect('penggunaan_bhp');
            return;
        }

        $data['title'] = 'Edit Penggunaan BHP';
        $data['penggunaan'] = $penggunaan;
        $data['bhp_list'] = $this->db->get_where('tb_inventaris', [
            'jenis' => 'Bahan', 'kondisi' => 'Baik', 'stok >' => 0
        ])->result();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('penggunaan_bhp/v_edit', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES UPDATE DATA PENGGUNAAN BHP
    // ==========================================
    public function update()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');
        $id_user_sess = $this->session->userdata('id_user');
        $id_penggunaan = $this->input->post('id_penggunaan', TRUE);

        if (!in_array($role_user, ['admin', 'laboran', 'guru', 'siswa'])) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('penggunaan_bhp');
            return;
        }

        $penggunaan_lama = $this->db->get_where('tb_penggunaan_bhp', ['id_penggunaan' => $id_penggunaan])->row();
        if (!$penggunaan_lama) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan.');
            redirect('penggunaan_bhp');
            return;
        }

        if (in_array($role_user, ['guru', 'siswa']) && $penggunaan_lama->id_user != $id_user_sess) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda hanya dapat menyunting pengajuan sendiri.');
            redirect('penggunaan_bhp');
            return;
        }

        if ($penggunaan_lama->status !== 'Pending') {
            $this->session->set_flashdata('error', 'Hanya pengajuan berstatus Pending yang dapat disunting.');
            redirect('penggunaan_bhp');
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id_barang', 'Barang', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('jumlah_pakai', 'Jumlah', 'required|integer|greater_than[0]');
        $this->form_validation->set_message('integer', '{field} harus berupa bilangan bulat.');
        $this->form_validation->set_message('greater_than', '{field} harus lebih dari nol.');

        if ($this->form_validation->run() == FALSE) {
            $this->edit($id_penggunaan);
            return;
        }

        $id_barang = $this->input->post('id_barang', TRUE);
        $jumlah_pakai = (int) $this->input->post('jumlah_pakai', TRUE);
        $barang = $this->db->get_where('tb_inventaris', [
            'id_barang' => $id_barang,
            'jenis' => 'Bahan',
            'kondisi' => 'Baik'
        ])->row();

        if (!$barang || $barang->stok < $jumlah_pakai) {
            $this->session->set_flashdata('error', 'Pilih bahan berkondisi Baik dengan stok yang mencukupi.');
            redirect('penggunaan_bhp/edit/' . $id_penggunaan);
            return;
        }

        $data_update = [
            'id_barang' => $id_barang,
            'jumlah_pakai' => $jumlah_pakai,
            'tgl_penggunaan' => $this->input->post('tgl_penggunaan', TRUE),
            'keterangan' => $this->input->post('keterangan', TRUE),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id_penggunaan', $id_penggunaan);
        $this->db->where('status', 'Pending');
        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('id_user', $id_user_sess);
        }
        $update = $this->db->update('tb_penggunaan_bhp', $data_update);

        if ($update && $this->db->affected_rows() > 0) {
            $this->session->set_flashdata('success', 'Pengajuan berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Tidak ada perubahan atau pengajuan sudah diproses.');
        }
        redirect('penggunaan_bhp');
    }

    // ==========================================
    // HAPUS DATA PENGGUNAAN BHP
    // ==========================================
    public function delete($id_penggunaan = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        if (empty($id_penggunaan)) {
            redirect('penggunaan_bhp');
            return;
        }

        $role_user    = strtolower($this->session->userdata('role') ?? '');
        $id_user_sess = $this->session->userdata('id_user');

        // PROTEKSI KETAT: Hanya role Admin yang diizinkan menghapus data penggunaan BHP
        if ($role_user !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur hapus data penggunaan BHP hanya dapat dilakukan oleh Admin.');
            redirect('penggunaan_bhp');
            return;
        }

        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $hasil = FALSE;
        $pesan = 'Gagal menghapus data penggunaan BHP.';

        try {
            if (!$this->db->trans_begin()) {
                throw new RuntimeException('Gagal memulai transaksi penghapusan BHP.');
            }

            $query = $this->db->query(
                'SELECT * FROM tb_penggunaan_bhp WHERE id_penggunaan = ? FOR UPDATE',
                [(int) $id_penggunaan]
            );
            $penggunaan = $query ? $query->row() : NULL;

            if (!$penggunaan) {
                $pesan = 'Data penggunaan BHP tidak ditemukan atau sudah diproses.';
            } else {
                $jumlah_pakai = (int) ($penggunaan->jumlah_pakai ?? 0);
                if ($penggunaan->status === 'Disetujui') {
                    if ($jumlah_pakai <= 0) {
                        $pesan = 'Jumlah penggunaan BHP tidak valid.';
                    } else {
                        $barang = $this->db->query(
                            'SELECT id_barang FROM tb_inventaris WHERE id_barang = ? FOR UPDATE',
                            [(int) $penggunaan->id_barang]
                        );
                        if (!$barang || $barang->num_rows() !== 1) {
                            $pesan = 'Barang BHP tidak ditemukan.';
                        } else {
                            $this->db->set('stok', 'stok + ' . $jumlah_pakai, FALSE);
                            $this->db->where('id_barang', (int) $penggunaan->id_barang);
                            $stok_update = $this->db->update('tb_inventaris');
                            if (!$stok_update || $this->db->affected_rows() !== 1) {
                                $pesan = 'Stok BHP gagal dipulihkan.';
                            }
                        }
                    }
                }

                if ($pesan === 'Gagal menghapus data penggunaan BHP.'
                    && $this->db->trans_status() !== FALSE) {
                    $this->db->where('id_penggunaan', (int) $id_penggunaan);
                    $delete = $this->db->delete('tb_penggunaan_bhp');
                    if ($delete && $this->db->affected_rows() === 1
                        && $this->db->trans_commit()) {
                        $hasil = TRUE;
                    }
                }
            }

            if (!$hasil) {
                $this->db->trans_rollback();
            }
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Gagal menghapus penggunaan BHP: ' . $e->getMessage());
        } finally {
            $this->db->db_debug = $debug;
        }

        if ($hasil) {
            $this->session->set_flashdata('success', 'Pengajuan penggunaan BHP berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', $pesan);
        }

        redirect('penggunaan_bhp');
    }

    // ==========================================
    // BATALKAN PENGAJUAN BHP (GURU & SISWA)
    // ==========================================
    public function batalkan($id_penggunaan = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        if (empty($id_penggunaan)) {
            redirect('penggunaan_bhp');
            return;
        }

        $role_user    = strtolower($this->session->userdata('role') ?? '');
        $id_user_login = $this->session->userdata('id_user');

        // Proteksi Role: Fitur pembatalan khusus untuk Guru dan Siswa
        if (!in_array($role_user, array('guru', 'siswa'))) {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur pembatalan pengajuan BHP khusus untuk Guru dan Siswa.');
            redirect('penggunaan_bhp');
            return;
        }

        $this->db->where('id_penggunaan', $id_penggunaan);
        $this->db->where('id_user', $id_user_login);
        $this->db->where('status', 'Pending');
        $delete = $this->db->delete('tb_penggunaan_bhp');

        if ($delete && $this->db->affected_rows() === 1) {
            $this->session->set_flashdata('success', 'Pengajuan penggunaan BHP berhasil dibatalkan.');
        } else {
            $this->session->set_flashdata('error', 'Data tidak ditemukan, bukan milik Anda, atau sudah diproses.');
        }

        redirect('penggunaan_bhp');
    }

    // ==========================================
    // FORM FILTER EXPORT LAPORAN PENGGUNAAN BHP
    // ==========================================
    public function export_laporan()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Proteksi Role: Hanya Admin, Kepsek, dan Laboran
        if (!in_array($role_user, array('admin', 'kepsek', 'laboran'))) {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur cetak laporan hanya untuk Admin, Kepsek, dan Laboran.');
            redirect('penggunaan_bhp');
            return;
        }

        $data['title'] = 'Export Laporan Penggunaan BHP';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('penggunaan_bhp/v_export_laporan', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES DOWNLOAD FILE EXCEL LAPORAN PENGGUNAAN BHP
    // ==========================================
    public function proses_export_excel()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        if (!in_array($role_user, array('admin', 'kepsek', 'laboran'))) {
            redirect('penggunaan_bhp');
            return;
        }

        $tgl_mulai   = $this->input->get('tgl_mulai', TRUE);
        $tgl_selesai = $this->input->get('tgl_selesai', TRUE);
        $status      = $this->input->get('status', TRUE);

        // Fetch data penggunaan join ke tb_users (pengguna/pengaju) dan tb_inventaris / tb_bhp (barang)
        $this->db->select('p.*, u.nama_lengkap AS nama_pengguna, i.nama_barang, i.satuan');
        $this->db->from('tb_penggunaan_bhp p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = p.id_barang', 'left');

        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $this->db->where("DATE(p.created_at) >=", $tgl_mulai);
            $this->db->where("DATE(p.created_at) <=", $tgl_selesai);
        }

        if (!empty($status) && $status !== 'Semua') {
            $this->db->where('p.status', $status);
        }

        $this->db->order_by('p.id_penggunaan', 'DESC');
        $data['laporan'] = $this->db->get()->result();

        // Data Penandatangan (Kepsek & Laboran)
        $data['kepsek']  = $this->db->get_where('tb_users', array('role' => 'kepsek'))->row();
        $data['laboran'] = $this->db->get_where('tb_users', array('role' => 'laboran'))->row();

        $data['tgl_mulai']   = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        // Header download berkas Excel
        $filename = "Laporan_Penggunaan_BHP_Lab_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $this->load->view('penggunaan_bhp/v_excel_laporan', $data);
    }
}
