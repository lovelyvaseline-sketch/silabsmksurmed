<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }

        // Proteksi Role: Hanya Admin yang diizinkan mengelola manajemen user
        $role_user = strtolower($this->session->userdata('role') ?? '');
        if ($role_user !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak! Manajemen User khusus untuk Admin.');
            redirect('dashboard');
        }
    }

    // ==========================================
    // 1. TAMPILKAN DAFTAR USER
    // ==========================================
    public function index()
    {
        $this->load->library('pagination');

        $keyword = $this->input->get('q', TRUE);
        $page    = ($this->input->get('per_page')) ? $this->input->get('per_page') : 0;

        // 1. Hitung Total Data (Filter Search jika ada)
        $this->db->from('tb_users');
        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('nama_lengkap', $keyword);
            $this->db->or_like('username', $keyword);
            $this->db->or_like('nip_nik_user', $keyword);
            $this->db->or_like('email', $keyword);
            $this->db->group_end();
        }
        $total_rows = $this->db->count_all_results();

        // 2. Konfigurasi Pagination CodeIgniter 3
        $config['base_url']             = site_url('users');
        $config['total_rows']           = $total_rows;
        $config['per_page']             = 10; // Jumlah data per halaman
        $config['page_query_string']    = TRUE;
        $config['query_string_segment'] = 'per_page';
        $config['reuse_query_string']   = TRUE; // Menjaga parameter search ?q= tetap ada

        // 3. Styling Template Pagination Tailwind CSS
        $config['full_tag_open']   = '<nav class="flex items-center gap-1">';
        $config['full_tag_close']  = '</nav>';

        $config['first_link']      = '<i class="fa-solid fa-angles-left"></i>';
        $config['first_tag_open']  = '<span class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">';
        $config['first_tag_close'] = '</span>';

        $config['last_link']       = '<i class="fa-solid fa-angles-right"></i>';
        $config['last_tag_open']   = '<span class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">';
        $config['last_tag_close']  = '</span>';

        $config['next_link']       = '<i class="fa-solid fa-chevron-right"></i>';
        $config['next_tag_open']   = '<span class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">';
        $config['next_tag_close']  = '</span>';

        $config['prev_link']       = '<i class="fa-solid fa-chevron-left"></i>';
        $config['prev_tag_open']   = '<span class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">';
        $config['prev_tag_close']  = '</span>';

        $config['cur_tag_open']    = '<span class="px-3 py-1.5 text-xs font-bold rounded-lg bg-teal-600 text-white shadow-sm">';
        $config['cur_tag_close']   = '</span>';

        $config['num_tag_open']    = '<span class="px-3 py-1.5 text-xs font-semibold rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-100 transition">';
        $config['num_tag_close']   = '</span>';

        $this->pagination->initialize($config);

        // 4. Fetch Data Terbatas (Limit & Offset)
        $this->db->from('tb_users');
        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('nama_lengkap', $keyword);
            $this->db->or_like('username', $keyword);
            $this->db->or_like('nip_nik_user', $keyword);
            $this->db->or_like('email', $keyword);
            $this->db->group_end();
        }
        $this->db->order_by('id_user', 'DESC');
        $this->db->limit($config['per_page'], $page);

        $data['users']      = $this->db->get()->result();
        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $total_rows;
        $data['start_num']  = $page + 1;
        $data['keyword']    = $keyword;
        $data['title']      = 'Manajemen Pengguna (Users)';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/index', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // 2. FORM TAMBAH USER
    // ==========================================
    public function tambah()
    {
        $data['title'] = 'Tambah Pengguna Baru';
        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/tambah', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES SIMPAN USER BARU
    // ==========================================
    public function simpan()
    {
        $username     = $this->input->post('username', TRUE);
        $nip_nik_user = $this->input->post('nip_nik_user', TRUE);
        $email        = $this->input->post('email', TRUE);

        // Validasi Duplikasi Username, NIP/NIK & Email
        $cek_username = $this->db->get_where('tb_users', array('username' => $username))->num_rows();
        if ($cek_username > 0) {
            $this->session->set_flashdata('error', 'Username sudah digunakan oleh akun lain!');
            redirect('users/tambah');
            return;
        }

        // Handle Upload Foto
        $nama_foto = NULL;
        if (!empty($_FILES['foto']['name'])) {
            $config['upload_path']   = './uploads/users/';
            $config['allowed_types'] = 'jpg|jpeg|png|webp';
            $config['max_size']      = 2048; // 2MB
            $config['file_name']     = 'user_' . time();

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('foto')) {
                $nama_foto = $this->upload->data('file_name');
            }
        }

        $data_user = array(
            'nip_nik_user' => $nip_nik_user,
            'username'     => $username,
            'password'     => password_hash($this->input->post('password'), PASSWORD_BCRYPT),
            'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
            'email'        => $email,
            'foto'         => $nama_foto,
            'role'         => $this->input->post('role', TRUE),
            'status_aktif' => $this->input->post('status_aktif', TRUE),
            'created_at'   => date('Y-m-d H:i:s')
        );

        $this->db->insert('tb_users', $data_user);
        $this->session->set_flashdata('success', 'Pengguna baru berhasil ditambahkan.');
        redirect('users');
    }

    // ==========================================
    // 3. FORM EDIT USER
    // ==========================================
    public function edit($id_user = NULL)
    {
        if (empty($id_user)) {
            redirect('users');
            return;
        }

        $user = $this->db->get_where('tb_users', array('id_user' => $id_user))->row();
        if (!$user) {
            $this->session->set_flashdata('error', 'Data pengguna tidak ditemukan.');
            redirect('users');
            return;
        }

        $data['title'] = 'Edit Data Pengguna';
        $data['user']  = $user;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/edit', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES UPDATE USER
    // ==========================================
    public function proses_edit()
    {
        $id_user = $this->input->post('id_user', TRUE);
        $user    = $this->db->get_where('tb_users', array('id_user' => $id_user))->row();

        if (!$user) {
            redirect('users');
            return;
        }

        $data_update = array(
            'nip_nik_user' => $this->input->post('nip_nik_user', TRUE),
            'username'     => $this->input->post('username', TRUE),
            'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
            'email'        => $this->input->post('email', TRUE),
            'role'         => $this->input->post('role', TRUE),
            'status_aktif' => $this->input->post('status_aktif', TRUE),
            'updated_at'   => date('Y-m-d H:i:s')
        );

        // Update password jika diisi
        $password_baru = $this->input->post('password');
        if (!empty($password_baru)) {
            $data_update['password'] = password_hash($password_baru, PASSWORD_BCRYPT);
        }

        // Handle Update Foto
        if (!empty($_FILES['foto']['name'])) {
            $config['upload_path']   = './uploads/users/';
            $config['allowed_types'] = 'jpg|jpeg|png|webp';
            $config['max_size']      = 2048;
            $config['file_name']     = 'user_' . time();

            $this->load->library('upload', $config);
            if ($this->upload->do_upload('foto')) {
                // Hapus foto lama jika ada
                if (!empty($user->foto) && file_exists('./uploads/users/' . $user->foto)) {
                    unlink('./uploads/users/' . $user->foto);
                }
                $data_update['foto'] = $this->upload->data('file_name');
            }
        }

        $this->db->where('id_user', $id_user);
        $this->db->update('tb_users', $data_update);

        $this->session->set_flashdata('success', 'Data pengguna berhasil diperbarui.');
        redirect('users');
    }

    // ==========================================
    // 4. HAPUS USER
    // ==========================================
    public function delete($id_user = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        if (empty($id_user)) {
            redirect('users');
            return;
        }

        // Mencegah Admin menghapus akunnya sendiri yang sedang dipakai login
        if ($id_user == $this->session->userdata('id_user')) {
            $this->session->set_flashdata('error', 'Gagal! Anda tidak dapat menghapus akun Anda sendiri saat sedang login.');
            redirect('users');
            return;
        }

        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $hasil = FALSE;
        $pesan = 'Pengguna gagal dihapus.';
        $user = NULL;

        try {
            if (!$this->db->trans_begin()) {
                throw new RuntimeException('Gagal memulai transaksi penghapusan pengguna.');
            }

            $query = $this->db->query(
                'SELECT * FROM tb_users WHERE id_user = ? FOR UPDATE',
                [(int) $id_user]
            );
            $user = $query ? $query->row() : NULL;

            if (!$user) {
                $pesan = 'Data pengguna tidak ditemukan.';
            } else {
                $relations = [
                    ['table' => 'tb_peminjaman', 'column' => 'id_user', 'label' => 'peminjaman'],
                    ['table' => 'tb_pengadaan', 'column' => 'id_pengaju', 'label' => 'pengadaan'],
                    ['table' => 'tb_penggunaan_bhp', 'column' => 'id_user', 'label' => 'penggunaan BHP'],
                    ['table' => 'tb_kerusakan_alat', 'column' => 'id_laboran', 'label' => 'laporan kerusakan'],
                ];

                foreach ($relations as $relation) {
                    $related = $this->db->query(
                        'SELECT 1 FROM ' . $relation['table']
                            . ' WHERE ' . $relation['column'] . ' = ? LIMIT 1 FOR UPDATE',
                        [(int) $id_user]
                    );

                    if (!$related) {
                        throw new RuntimeException(
                            'Gagal memeriksa relasi ' . $relation['label'] . '.'
                        );
                    }

                    if ($related->num_rows() > 0) {
                        $pesan = 'Pengguna memiliki riwayat ' . $relation['label']
                            . '. Nonaktifkan akun, bukan menghapusnya.';
                        break;
                    }
                }

                if ($pesan === 'Pengguna gagal dihapus.') {
                    $this->db->where('id_user', (int) $id_user);
                    $deleted = $this->db->delete('tb_users');
                    if ($deleted && $this->db->affected_rows() === 1
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
            log_message('error', 'Gagal menghapus pengguna: ' . $e->getMessage());
        } finally {
            $this->db->db_debug = $debug;
        }

        if ($hasil) {
            if (!empty($user->foto) && file_exists('./uploads/users/' . $user->foto)
                && !unlink('./uploads/users/' . $user->foto)) {
                log_message('error', 'Gagal menghapus foto pengguna: ' . $user->foto);
            }
            $this->session->set_flashdata('success', 'Pengguna berhasil dihapus.');
        } else {
            $this->session->set_flashdata('error', $pesan);
        }
        redirect('users');
    }

    // ==========================================
    // FORM FILTER EXPORT DATA USER
    // ==========================================
    public function export_laporan()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        // Proteksi Role: Fitur ini khusus untuk Admin
        if ($role_user !== 'admin') {
            $this->session->set_flashdata('error', 'Akses ditolak! Fitur export data user khusus untuk Admin.');
            redirect('users');
            return;
        }

        $data['title'] = 'Export Data Pengguna (Users)';

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('users/v_export_user', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES DOWNLOAD FILE EXCEL DATA USER
    // ==========================================
    public function proses_export_excel()
    {
        $role_user = strtolower($this->session->userdata('role') ?? '');

        if ($role_user !== 'admin') {
            redirect('users');
            return;
        }

        $filter_role   = $this->input->get('role', TRUE);
        $filter_status = $this->input->get('status_aktif', TRUE);

        $this->db->from('tb_users');

        if (!empty($filter_role) && $filter_role !== 'Semua') {
            $this->db->where('role', $filter_role);
        }

        if ($filter_status !== '' && $filter_status !== NULL && $filter_status !== 'Semua') {
            $this->db->where('status_aktif', $filter_status);
        }

        $this->db->order_by('role', 'ASC');
        $this->db->order_by('nama_lengkap', 'ASC');
        $data['users'] = $this->db->get()->result();

        // Data Penandatangan (Kepsek & Laboran)
        $data['kepsek']  = $this->db->get_where('tb_users', array('role' => 'kepsek'))->row();
        $data['laboran'] = $this->db->get_where('tb_users', array('role' => 'laboran'))->row();

        $data['filter_role']   = $filter_role;
        $data['filter_status'] = $filter_status;

        // Header download berkas Excel
        $filename = "Laporan_Data_Pengguna_Users_" . date('Ymd_His') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");

        $this->load->view('users/v_excel_user', $data);
    }
}
