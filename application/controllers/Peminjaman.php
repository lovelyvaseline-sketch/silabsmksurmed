<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * @property CI_Session $session
 * @property M_Inventaris $M_Inventaris
 * @property M_Peminjaman $M_Peminjaman
 * @property CI_Session $input
 * @property CI_Session $db
 */
class Peminjaman extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('M_Peminjaman');
        $this->load->model('M_Inventaris');
        $this->load->model('M_Auth');
    }

    public function index()
    {
        $data['title'] = 'Peminjaman Alat';

        // 1. Load Library Pagination
        $this->load->library('pagination');

        $role_user = strtolower($this->session->userdata('role') ?? '');
        $id_user   = $this->session->userdata('id_user');

        // 2. Ambil parameter limit baris (default 10) & keyword pencarian
        $per_page = (int)$this->input->get('limit', TRUE);
        if (!in_array($per_page, [5, 10, 25, 50])) {
            $per_page = 10;
        }

        $keyword = trim($this->input->get('q', TRUE) ?? '');

        // 3. Hitung total data berdasarkan filter role & pencarian
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris b', 'b.id_barang = p.id_barang', 'left');

        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('p.id_user', $id_user);
        }

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.kode_peminjaman', $keyword);
            $this->db->or_like('u.nama_lengkap', $keyword);
            $this->db->or_like('b.nama_barang', $keyword);
            $this->db->or_like('p.status', $keyword);
            $this->db->group_end();
        }

        $total_rows = $this->db->count_all_results();

        // 4. Konfigurasi Pagination
        $config['base_url']             = base_url('peminjaman/index');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = $per_page;
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string']   = TRUE; // Menjaga parameter 'q' dan 'limit' di URL

        // Styling Pagination Tailwind CSS
        $config['full_tag_open']   = '<ul class="inline-flex items-center gap-1 text-xs">';
        $config['full_tag_close']  = '</ul>';

        $config['first_link']      = '&laquo;';
        $config['first_tag_open']  = '<li>';
        $config['first_tag_close'] = '</li>';

        $config['last_link']       = '&raquo;';
        $config['last_tag_open']   = '<li>';
        $config['last_tag_close']  = '</li>';

        $config['next_link']       = '<i class="fa-solid fa-chevron-right text-[10px]"></i>';
        $config['next_tag_open']   = '<li>';
        $config['next_tag_close']  = '</li>';

        $config['prev_link']       = '<i class="fa-solid fa-chevron-left text-[10px]"></i>';
        $config['prev_tag_open']   = '<li>';
        $config['prev_tag_close']  = '</li>';

        $config['cur_tag_open']    = '<li><span class="flex items-center justify-center px-3 h-8 text-white bg-teal-600 rounded-lg font-semibold shadow-sm">';
        $config['cur_tag_close']   = '</span></li>';

        $config['num_tag_open']    = '<li>';
        $config['num_tag_close']   = '</li>';

        $config['attributes']      = ['class' => 'flex items-center justify-center px-3 h-8 text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition'];

        $this->pagination->initialize($config);

        // 5. Ambil Data Peminjaman dengan Offset & Pencarian
        $page   = (int) $this->input->get('page');
        $offset = ($page > 0) ? $page : 0;

        $this->db->select('p.*, u.nama_lengkap AS peminjam, u.role, b.nama_barang, b.kode_barcode');
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris b', 'b.id_barang = p.id_barang', 'left');

        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('p.id_user', $id_user);
        }

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('p.kode_peminjaman', $keyword);
            $this->db->or_like('u.nama_lengkap', $keyword);
            $this->db->or_like('b.nama_barang', $keyword);
            $this->db->or_like('p.status', $keyword);
            $this->db->group_end();
        }

        $this->db->order_by('p.id_peminjaman', 'DESC');
        $this->db->limit($per_page, $offset);
        $data['peminjaman'] = $this->db->get()->result_array();

        // 6. Data pendukung ke View
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $total_rows;
        $data['offset']     = $offset;
        $data['per_page']   = $per_page;
        $data['keyword']    = $keyword;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('peminjaman/index', $data);
        $this->load->view('templates/footer');
    }

    public function return_item($id_peminjaman)
    {
        redirect('peminjaman/konfirmasi_kembali/' . (int) $id_peminjaman);
    }


    // ==========================================
    // FORM TAMBAH PEMINJAMAN
    // ==========================================
    public function tambah()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        if ($role_user === 'kepsek') {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('peminjaman');
            return;
        }

        $data['title'] = 'Form Pinjam Alat Laboratorium';

        $data['alat_list'] = $this->db->get_where('tb_inventaris', [
            'jenis' => 'Alat',
            'kondisi' => 'Baik',
            'stok >' => 0
        ])->result();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('peminjaman/tambah', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES SIMPAN PEMINJAMAN
    // ==========================================
    public function simpan()
    {
        $role = strtolower($this->session->userdata('role') ?? '');

        if (!in_array($role, ['admin', 'laboran', 'guru', 'siswa'], TRUE)) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('peminjaman');
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id_barang', 'Barang', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('jumlah_pinjam', 'Jumlah', 'required|integer|greater_than[0]');
        $this->form_validation->set_message('integer', '{field} harus berupa bilangan bulat.');
        $this->form_validation->set_message('greater_than', '{field} harus lebih dari nol.');

        if ($this->form_validation->run() == FALSE) {
            $this->tambah();
            return;
        }

        // Tangkap input dari POST secara fleksibel
        $kode_peminjaman     = $this->input->post('kode_peminjaman', TRUE);
        $id_barang           = $this->input->post('id_barang', TRUE);
        $jumlah_pinjam       = (int) $this->input->post('jumlah_pinjam', TRUE);
        $tgl_pinjam          = $this->input->post('tgl_pinjam', TRUE) ?: date('Y-m-d H:i:s');
        $tgl_kembali_rencana = $this->input->post('tgl_kembali_rencana', TRUE);
        $catatan             = $this->input->post('catatan', TRUE) ?: ($this->input->post('keterangan', TRUE) ?? '-');

        // FORCE GENERATE KODE JIKA KOSONG / NULL
        if (empty(trim($kode_peminjaman ?? ''))) {
            $kode_peminjaman = 'PJM-' . date('Ymd') . '-' . rand(100, 999);
        }

        // FORCE SET TANGGAL KEMBALI JIKA KOSONG / NULL
        if (empty(trim($tgl_kembali_rencana ?? ''))) {
            $tgl_kembali_rencana = date('Y-m-d', strtotime('+1 day'));
        }

        // Validasi Barang
        if (empty($id_barang)) {
            $this->session->set_flashdata('error', 'Gagal! Silakan pilih alat yang ingin dipinjam.');
            redirect('peminjaman/tambah');
            return;
        }

        // Validasi Stok
        $barang = $this->db->get_where('tb_inventaris', [
            'id_barang' => $id_barang,
            'jenis' => 'Alat',
            'kondisi' => 'Baik'
        ])->row();

        if (!$barang) {
            $this->session->set_flashdata('error', 'Pilih alat yang tersedia dan berkondisi Baik.');
            redirect('peminjaman/tambah');
            return;
        }
        if (!$barang || $barang->stok < $jumlah_pinjam) {
            $this->session->set_flashdata('error', 'Gagal! Stok alat tidak mencukupi (Tersedia: ' . ($barang->stok ?? 0) . ').');
            redirect('peminjaman/tambah');
            return;
        }

        // Status Awal
        $status_awal = in_array($role, ['admin', 'laboran']) ? 'Dipinjam' : 'Pending';

        // Array Insert Data
        $data_insert = [
            'kode_peminjaman'     => $kode_peminjaman,
            'id_user'             => $this->session->userdata('id_user'),
            'id_barang'           => $id_barang,
            'jumlah_pinjam'       => $jumlah_pinjam,
            'tgl_pinjam'          => $tgl_pinjam,
            'tgl_kembali_rencana' => $tgl_kembali_rencana,
            'status'              => $status_awal,
            'catatan'             => $catatan,
            'created_at'          => date('Y-m-d H:i:s')
        ];

        $this->load->model('M_Inventaris');
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $berhasil = FALSE;

        try {
            if (!$this->db->trans_begin()) {
                throw new RuntimeException('Gagal memulai transaksi.');
            }

            $insert = $this->db->insert('tb_peminjaman', $data_insert);
            $stok = TRUE;
            if ($insert && $status_awal === 'Dipinjam') {
                $stok = $this->M_Inventaris->kurangi_stok($id_barang, $jumlah_pinjam, 'Alat');
            }

            if ($insert && $stok && $this->db->trans_status() === TRUE) {
                $berhasil = $this->db->trans_commit();
            }
            if (!$berhasil) {
                $this->db->trans_rollback();
            }
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Gagal menyimpan peminjaman: ' . $e->getMessage());
        } finally {
            $this->db->db_debug = $db_debug;
        }

        if ($berhasil) {
            $pesan = ($status_awal === 'Dipinjam')
                ? 'Peminjaman berhasil dicatat dan stok alat telah dipotong.'
                : 'Pengajuan peminjaman berhasil dibuat, menunggu persetujuan Laboran.';
            $this->session->set_flashdata('success', $pesan);
        } else {
            $this->session->set_flashdata('error', 'Gagal menyimpan transaksi. Tidak ada perubahan stok yang disimpan.');
        }

        redirect('peminjaman');
    }

    public function detail($id_peminjaman = NULL)
    {
        if (empty($id_peminjaman)) {
            redirect('peminjaman');
            return;
        }

        // Query Join untuk mengambil detail peminjaman beserta nama peminjam dan nama barang
        $this->db->select('p.*, u.nama_lengkap AS peminjam, i.nama_barang, i.satuan');
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = p.id_barang', 'left');
        $this->db->where('p.id_peminjaman', $id_peminjaman);

        $role_user = strtolower($this->session->userdata('role') ?? '');
        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('p.id_user', $this->session->userdata('id_user'));
        }

        $peminjaman = $this->db->get()->row();

        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data peminjaman tidak ditemukan.');
            redirect('peminjaman');
            return;
        }

        $data['title']      = 'Detail Peminjaman Alat';
        $data['peminjaman'] = $peminjaman;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('peminjaman/detail', $data);
        $this->load->view('templates/footer');
    }

    // Menampilkan Form Edit Peminjaman
    public function edit($id_peminjaman = NULL)
    {
        if (empty($id_peminjaman)) {
            redirect('peminjaman');
            return;
        }

        $role_user = strtolower($this->session->userdata('role') ?? '');
        $id_user_sess = $this->session->userdata('id_user');
        if (!in_array($role_user, ['admin', 'laboran', 'guru', 'siswa'])) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('peminjaman');
            return;
        }

        $peminjaman = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan.');
            redirect('peminjaman');
            return;
        }

        if (in_array($role_user, ['guru', 'siswa']) && $peminjaman->id_user != $id_user_sess) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda hanya dapat menyunting pengajuan sendiri.');
            redirect('peminjaman');
            return;
        }

        if ($peminjaman->status !== 'Pending') {
            $this->session->set_flashdata('error', 'Hanya pengajuan berstatus Pending yang dapat disunting.');
            redirect('peminjaman');
            return;
        }

        $data['title'] = 'Edit Data Peminjaman';
        $data['peminjaman'] = $peminjaman;
        $data['alat_list'] = $this->db->get_where('tb_inventaris', [
            'jenis' => 'Alat', 'kondisi' => 'Baik', 'stok >' => 0
        ])->result();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('peminjaman/edit', $data);
        $this->load->view('templates/footer');
    }


    // Proses Simpan Update Peminjaman
    public function update()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');
        $id_user_sess = $this->session->userdata('id_user');
        $id_peminjaman = $this->input->post('id_peminjaman', TRUE);

        if (!in_array($role_user, ['admin', 'laboran', 'guru', 'siswa'])) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('peminjaman');
            return;
        }

        $peminjaman_lama = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
        if (!$peminjaman_lama) {
            $this->session->set_flashdata('error', 'Data tidak ditemukan.');
            redirect('peminjaman');
            return;
        }

        if (in_array($role_user, ['guru', 'siswa']) && $peminjaman_lama->id_user != $id_user_sess) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda hanya dapat menyunting pengajuan sendiri.');
            redirect('peminjaman');
            return;
        }

        if ($peminjaman_lama->status !== 'Pending') {
            $this->session->set_flashdata('error', 'Hanya pengajuan berstatus Pending yang dapat disunting.');
            redirect('peminjaman');
            return;
        }

        $this->load->library('form_validation');
        $this->form_validation->set_rules('id_barang', 'Barang', 'required|integer|greater_than[0]');
        $this->form_validation->set_rules('jumlah_pinjam', 'Jumlah', 'required|integer|greater_than[0]');
        $this->form_validation->set_message('integer', '{field} harus berupa bilangan bulat.');
        $this->form_validation->set_message('greater_than', '{field} harus lebih dari nol.');
        $this->form_validation->set_rules('tgl_pinjam', 'Tanggal Pinjam', 'required');
        $this->form_validation->set_rules('tgl_kembali_rencana', 'Rencana Kembali', 'required');

        if ($this->form_validation->run() == FALSE) {
            $this->edit($id_peminjaman);
            return;
        }

        $id_barang = $this->input->post('id_barang', TRUE);
        $jumlah_pinjam = (int) $this->input->post('jumlah_pinjam', TRUE);
        $barang = $this->db->get_where('tb_inventaris', [
            'id_barang' => $id_barang,
            'jenis' => 'Alat',
            'kondisi' => 'Baik'
        ])->row();

        if (!$barang || $barang->stok < $jumlah_pinjam) {
            $this->session->set_flashdata('error', 'Pilih alat berkondisi Baik dengan stok yang mencukupi.');
            redirect('peminjaman/edit/' . $id_peminjaman);
            return;
        }

        $data_update = [
            'id_barang' => $id_barang,
            'jumlah_pinjam' => $jumlah_pinjam,
            'tgl_pinjam' => $this->input->post('tgl_pinjam', TRUE),
            'tgl_kembali_rencana' => $this->input->post('tgl_kembali_rencana', TRUE),
            'catatan' => $this->input->post('catatan', TRUE),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->where('status', 'Pending');
        if (in_array($role_user, ['guru', 'siswa'])) {
            $this->db->where('id_user', $id_user_sess);
        }
        $update = $this->db->update('tb_peminjaman', $data_update);

        if ($update && $this->db->affected_rows() > 0) {
            $this->session->set_flashdata('success', 'Pengajuan berhasil diperbarui.');
        } else {
            $this->session->set_flashdata('error', 'Tidak ada perubahan atau pengajuan sudah diproses.');
        }
        redirect('peminjaman');
    }



    // =========================== APPROVAL PEMINJAMAN ====================================
    // Method untuk menangani aksi approval dari Laboran/Admin
    public function update_status($id_peminjaman = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        $status_tujuan = $this->input->post('status_tujuan', TRUE);

        // Proteksi: Hanya Admin dan Laboran yang berhak melakukan approval
        $role = strtolower($this->session->userdata('role') ?? '');
        if (!in_array($role, ['admin', 'laboran'])) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak memiliki wewenang approval.');
            redirect('peminjaman');
            return;
        }

        if (empty($id_peminjaman) || empty($status_tujuan)) {
            redirect('peminjaman');
            return;
        }

        // Validasi status yang diizinkan
        $status_valid  = ['Dipinjam', 'Ditolak'];
        $status_decode = $status_tujuan;

        if (!in_array($status_decode, $status_valid, TRUE)) {
            $this->session->set_flashdata('error', 'Status approval tidak valid.');
            redirect('peminjaman');
            return;
        }

        // Eksekusi perubahan status dan stok di Model
        $this->load->model('M_Peminjaman');
        $hasil = $this->M_Peminjaman->proses_approval($id_peminjaman, $status_decode);

        if ($hasil === 'stok_habis') {
            $this->session->set_flashdata('error', 'Barang tidak tersedia, kondisi berubah, atau stok tidak mencukupi.');
        } elseif ($hasil === 'sudah_diproses') {
            $this->session->set_flashdata('error', 'Pengajuan sudah berubah atau diproses. Muat ulang halaman.');
        } elseif ($hasil === 'status_tidak_valid') {
            $this->session->set_flashdata('error', 'Perubahan status tidak sah.');
        } elseif ($hasil === TRUE) {
            $this->session->set_flashdata('success', 'Status peminjaman berhasil diperbarui menjadi ' . $status_decode . '.');
        } else {
            $this->session->set_flashdata('error', 'Terjadi kesalahan sistem saat memperbarui status.');
        }

        redirect('peminjaman');
    }
    //==================================================================================

    // ==========================================
    // HAPUS / BATALKAN PEMINJAMAN ALAT
    // ==========================================
    public function delete($id_peminjaman = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        if (empty($id_peminjaman)) {
            redirect('peminjaman');
            return;
        }

        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Proteksi Ketat: Hanya Admin yang diizinkan menghapus data peminjaman
        if ($role_user !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur hapus data peminjaman alat hanya dapat dilakukan oleh Admin.');
            redirect('peminjaman');
            return;
        }

        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $hasil = FALSE;
        $pesan = 'Gagal menghapus data peminjaman alat.';

        try {
            if (!$this->db->trans_begin()) {
                throw new RuntimeException('Gagal memulai transaksi penghapusan peminjaman.');
            }

            $query = $this->db->query(
                'SELECT * FROM tb_peminjaman WHERE id_peminjaman = ? FOR UPDATE',
                [(int) $id_peminjaman]
            );
            $peminjaman = $query ? $query->row() : NULL;

            if (!$peminjaman) {
                $pesan = 'Data peminjaman tidak ditemukan atau sudah diproses.';
            } else {
                $laporan = $this->db->query(
                    'SELECT id_kerusakan FROM tb_kerusakan_alat WHERE id_peminjaman = ? LIMIT 1 FOR UPDATE',
                    [(int) $peminjaman->id_peminjaman]
                );

                if (!$laporan) {
                    $pesan = 'Gagal memeriksa laporan kerusakan.';
                } elseif ($laporan->num_rows() > 0) {
                    $pesan = 'Peminjaman memiliki laporan kerusakan dan tidak dapat dihapus.';
                } else {
                    $jumlah_pinjam = (int) ($peminjaman->jumlah_pinjam ?? 0);
                    if ($peminjaman->status === 'Dipinjam') {
                        if ($jumlah_pinjam <= 0) {
                            $pesan = 'Jumlah peminjaman tidak valid.';
                        } else {
                            $barang = $this->db->query(
                                'SELECT id_barang FROM tb_inventaris WHERE id_barang = ? FOR UPDATE',
                                [(int) $peminjaman->id_barang]
                            );
                            if (!$barang || $barang->num_rows() !== 1) {
                                $pesan = 'Barang peminjaman tidak ditemukan.';
                            } else {
                                $this->db->set('stok', 'stok + ' . $jumlah_pinjam, FALSE);
                                $this->db->where('id_barang', (int) $peminjaman->id_barang);
                                $stok_update = $this->db->update('tb_inventaris');
                                if (!$stok_update || $this->db->affected_rows() !== 1) {
                                    $pesan = 'Stok alat gagal dipulihkan.';
                                }
                            }
                        }
                    }

                    if ($pesan === 'Gagal menghapus data peminjaman alat.'
                        && $this->db->trans_status() !== FALSE) {
                        $this->db->where('id_peminjaman', (int) $id_peminjaman);
                        $delete = $this->db->delete('tb_peminjaman');
                        if ($delete && $this->db->affected_rows() === 1
                            && $this->db->trans_commit()) {
                            $hasil = TRUE;
                        }
                    }
                }
            }

            if (!$hasil) {
                $this->db->trans_rollback();
            }
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Gagal menghapus peminjaman: ' . $e->getMessage());
        } finally {
            $this->db->db_debug = $debug;
        }

        if ($hasil) {
            $this->session->set_flashdata('success', 'Data peminjaman alat berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', $pesan);
        }

        redirect('peminjaman');
    }

    // ==========================================
    // FORM KONFIRMASI KEMBALI ALAT (LABORAN & ADMIN)
    // ==========================================
    public function konfirmasi_kembali($id_peminjaman = NULL)
    {
        if (empty($id_peminjaman)) {
            redirect('peminjaman');
            return;
        }

        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Hak akses khusus Admin & Laboran
        if (!in_array($role_user, ['admin', 'laboran'])) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('peminjaman');
            return;
        }

        // Query Detail Peminjaman
        $this->db->select('p.*, u.nama_lengkap AS peminjam, i.nama_barang');
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = p.id_barang', 'left');
        $this->db->where('p.id_peminjaman', $id_peminjaman);

        $peminjaman = $this->db->get()->row();

        if (!$peminjaman) {
            $this->session->set_flashdata('error', 'Data peminjaman tidak ditemukan.');
            redirect('peminjaman');
            return;
        }

        $data['title']      = 'Cek Pengembalian Alat';
        $data['peminjaman'] = $peminjaman;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('peminjaman/v_konfirmasi_kembali', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES PENGEMBALIAN & RESTORASI STOK ALAT
    // ==========================================
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

    public function batalkan($id_peminjaman = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        if (empty($id_peminjaman)) {
            redirect('peminjaman');
            return;
        }

        $role_user    = strtolower($this->session->userdata('role') ?? '');
        $id_user_login = $this->session->userdata('id_user');

        // Proteksi Role: Fitur batalkan khusus untuk Guru dan Siswa
        if (!in_array($role_user, array('guru', 'siswa'))) {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur pembatalan khusus untuk Guru dan Siswa.');
            redirect('peminjaman');
            return;
        }

        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->where('id_user', $id_user_login);
        $this->db->where('status', 'Pending');
        $delete = $this->db->delete('tb_peminjaman');

        if ($delete && $this->db->affected_rows() === 1) {
            $this->session->set_flashdata('success', 'Pengajuan peminjaman alat berhasil dibatalkan.');
        } else {
            $this->session->set_flashdata('error', 'Data tidak ditemukan, bukan milik Anda, atau sudah diproses.');
        }

        redirect('peminjaman');
    }

    // ==========================================
    // FORM FILTER EXPORT LAPORAN PEMINJAMAN
    // ==========================================
    public function export_laporan()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Proteksi Role: Hanya Admin, Kepsek, dan Laboran
        if (!in_array($role_user, array('admin', 'kepsek', 'laboran'))) {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur cetak laporan hanya untuk Admin, Kepsek, dan Laboran.');
            redirect('peminjaman');
            return;
        }

        $data['title'] = 'Export Laporan Peminjaman Alat Praktikum';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('peminjaman/v_export_laporan', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES DOWNLOAD FILE EXCEL LAPORAN PEMINJAMAN
    // ==========================================
    public function proses_export_excel()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        if (!in_array($role_user, array('admin', 'kepsek', 'laboran'))) {
            redirect('peminjaman');
            return;
        }

        $tgl_mulai   = $this->input->get('tgl_mulai', TRUE);
        $tgl_selesai = $this->input->get('tgl_selesai', TRUE);
        $status      = $this->input->get('status', TRUE);

        // PERBAIKAN: Gunakan i.id_barang = p.id_barang
        $this->db->select('p.*, u.nama_lengkap AS nama_peminjam, i.nama_barang');
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris i', 'i.id_barang = p.id_barang', 'left'); // <-- PERBAIKAN DI SINI

        if (!empty($tgl_mulai) && !empty($tgl_selesai)) {
            $this->db->where("DATE(p.tgl_pinjam) >=", $tgl_mulai);
            $this->db->where("DATE(p.tgl_pinjam) <=", $tgl_selesai);
        }

        if (!empty($status) && $status !== 'Semua') {
            $this->db->where('p.status', $status);
        }

        $this->db->order_by('p.id_peminjaman', 'DESC');
        $data['laporan'] = $this->db->get()->result();

        // Data Penandatangan (Kepsek & Laboran)
        $data['kepsek']  = $this->db->get_where('tb_users', array('role' => 'kepsek'))->row();
        $data['laboran'] = $this->db->get_where('tb_users', array('role' => 'laboran'))->row();

        $data['tgl_mulai']   = $tgl_mulai;
        $data['tgl_selesai'] = $tgl_selesai;

        // Header download berkas Excel
        $filename = "Laporan_Peminjaman_Alat_Lab_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $this->load->view('peminjaman/v_excel_laporan', $data);
    }
}
