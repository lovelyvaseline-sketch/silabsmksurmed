<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Inventaris extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
        $this->load->library('pagination');
    }

    public function index()
    {
        redirect('inventaris/alat');
    }

    public function alat()
    {
        $this->_render_inventaris('Alat', 'Stok Alat Laboratorium');
    }

    public function bahan()
    {
        $this->_render_inventaris('Bahan', 'Stok Bahan Habis Pakai (BHP)');
    }

    private function _render_inventaris($jenis, $title)
    {
        $data['title'] = $title;
        $data['jenis'] = $jenis;

        $per_page = (int) $this->input->get('limit', TRUE);
        if (!in_array($per_page, [5, 10, 25, 50])) {
            $per_page = 10;
        }

        $keyword = trim($this->input->get('q', TRUE) ?? '');

        // 1. Query Filter
        $this->db->from('tb_inventaris');
        if ($this->db->field_exists('jenis', 'tb_inventaris')) {
            $this->db->where('jenis', $jenis);
        } else {
            if ($jenis === 'Bahan') {
                $this->db->like('kategori', 'Bahan');
            } else {
                $this->db->not_like('kategori', 'Bahan');
            }
        }

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('nama_barang', $keyword);
            $this->db->or_like('kode_barcode', $keyword);
            $this->db->or_like('kategori', $keyword);
            $this->db->group_end();
        }

        $total_rows = $this->db->count_all_results();

        // 2. Pagination Config
        $config['base_url']             = base_url('inventaris/' . strtolower($jenis));
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

        $page   = (int) $this->input->get('page');
        $offset = ($page > 0) ? $page : 0;

        // 3. Query Utama Data
        if ($this->db->field_exists('jenis', 'tb_inventaris')) {
            $this->db->where('jenis', $jenis);
        } else {
            if ($jenis === 'Bahan') {
                $this->db->like('kategori', 'Bahan');
            } else {
                $this->db->not_like('kategori', 'Bahan');
            }
        }

        if (!empty($keyword)) {
            $this->db->group_start();
            $this->db->like('nama_barang', $keyword);
            $this->db->or_like('kode_barcode', $keyword);
            $this->db->or_like('kategori', $keyword);
            $this->db->group_end();
        }

        $this->db->select('tb_inventaris.*');

        if ($jenis === 'Alat') {
            $this->db->select(
                "(SELECT COALESCE(SUM(k.jumlah_rusak), 0)
                  FROM tb_kerusakan_alat k
                  WHERE k.id_barang = tb_inventaris.id_barang
                    AND k.status_perbaikan IN (
                        'Menunggu Perbaikan',
                        'Sedang Diperbaiki'
                    )
                ) AS sedang_rusak",
                FALSE
            );
        }

        $this->db->order_by('id_barang', 'DESC');
        $data['inventaris'] = $this->db
            ->get('tb_inventaris', $per_page, $offset)
            ->result();

        $data['pagination'] = $this->pagination->create_links();
        $data['total_rows'] = $total_rows;
        $data['offset']     = $offset;
        $data['per_page']   = $per_page;
        $data['keyword']    = $keyword;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inventaris/index', $data);
        $this->load->view('templates/footer');
    }

    private function _cek_akses_crud()
    {
        $role = strtolower($this->session->userdata('role') ?? '');
        if (!in_array($role, ['admin', 'laboran'])) {
            $this->session->set_flashdata('error', 'Akses ditolak! Anda tidak memiliki wewenang.');
            redirect('dashboard');
            exit;
        }
    }

    public function tambah($jenis = 'Alat')
    {
        $this->_cek_akses_crud();

        $jenis = (strtolower($jenis) === 'bahan') ? 'Bahan' : 'Alat';

        $data['title'] = 'Tambah ' . $jenis . ' Baru';
        $data['jenis'] = $jenis;

        $prefix = ($jenis === 'Bahan') ? 'BHP-' : 'ALT-';
        $data['auto_barcode'] = $prefix . date('Ymd') . '-' . rand(100, 999);

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inventaris/tambah', $data);
        $this->load->view('templates/footer');
    }

    public function simpan()
    {
        $this->_cek_akses_crud();

        $this->load->library('form_validation');
        $this->form_validation->set_rules('kode_barcode', 'Kode Barcode', 'required|trim|is_unique[tb_inventaris.kode_barcode]');
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('stok', 'Stok', 'required|integer|greater_than_equal_to[0]');
        $this->form_validation->set_rules('satuan', 'Satuan', 'required|trim');
        $this->form_validation->set_rules('kondisi', 'Kondisi', 'required|trim');
        $this->form_validation->set_rules('tgl_pengadaan', 'Tanggal Pengadaan', 'required|trim');
        $this->form_validation->set_rules('lokasi_penyimpanan', 'Lokasi Penyimpanan', 'required|trim');

        $jenis = $this->input->post('jenis', TRUE) ?? 'Alat';

        if ($this->form_validation->run() == FALSE) {
            $this->tambah($jenis);
        } else {
            $data_insert = [
                'kode_barcode'       => $this->input->post('kode_barcode', TRUE),
                'nama_barang'        => $this->input->post('nama_barang', TRUE),
                'kategori'           => $this->input->post('kategori', TRUE),
                'stok'               => (int) $this->input->post('stok', TRUE),
                'satuan'             => $this->input->post('satuan', TRUE),
                'kondisi'            => $this->input->post('kondisi', TRUE),
                'tgl_pengadaan'      => $this->input->post('tgl_pengadaan', TRUE),
                'lokasi_penyimpanan' => $this->input->post('lokasi_penyimpanan', TRUE),
                'created_at'         => date('Y-m-d H:i:s')
            ];

            if ($this->db->field_exists('jenis', 'tb_inventaris')) {
                $data_insert['jenis'] = $jenis;
            }

            $insert = $this->db->insert('tb_inventaris', $data_insert);

            if ($insert) {
                $this->session->set_flashdata('success', 'Data ' . strtolower($jenis) . ' berhasil ditambahkan.');
            } else {
                $this->session->set_flashdata('error', 'Gagal menambahkan data.');
            }

            redirect('inventaris/' . strtolower($jenis));
        }
    }

    public function delete($id_barang)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        $this->_cek_akses_crud();

        $cek_pinjam = $this->db->get_where('tb_peminjaman', ['id_barang' => $id_barang])->num_rows();
        if ($cek_pinjam > 0) {
            $this->session->set_flashdata('error', 'Gagal menghapus! Item ini masih terikat transaksi peminjaman.');
            redirect($_SERVER['HTTP_REFERER']);
            return;
        }

        $this->db->where('id_barang', $id_barang);
        $this->db->delete('tb_inventaris');

        $this->session->set_flashdata('success', 'Data inventaris berhasil dihapus.');
        redirect($_SERVER['HTTP_REFERER']);
    }


    public function edit($id_barang = NULL)
    {
        $this->_cek_akses_crud();

        if (empty($id_barang)) {
            redirect('inventaris/alat');
        }

        // Ambil data item berdasarkan ID
        $barang = $this->db->get_where('tb_inventaris', ['id_barang' => $id_barang])->row();

        if (!$barang) {
            $this->session->set_flashdata('error', 'Data barang tidak ditemukan.');
            redirect('inventaris/alat');
        }

        // Tentukan jenis berdasarkan data di database
        $jenis = isset($barang->jenis) ? $barang->jenis : ($this->input->get('jenis') ?? 'Alat');

        $data['title']  = 'Edit Data ' . $jenis;
        $data['jenis']  = $jenis;
        $data['barang'] = $barang;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('inventaris/edit', $data);
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES UPDATE DATA KE DATABASE
    // ==========================================
    public function update()
    {
        $this->_cek_akses_crud();

        $id_barang = $this->input->post('id_barang', TRUE);
        $jenis     = $this->input->post('jenis', TRUE) ?? 'Alat';

        $this->load->library('form_validation');
        // CATATAN: Jangan pasang validasi is_unique pada kode_barcode di sini karena tidak diubah
        $this->form_validation->set_rules('nama_barang', 'Nama Barang', 'required|trim');
        $this->form_validation->set_rules('kategori', 'Kategori', 'required|trim');
        $this->form_validation->set_rules('stok', 'Stok', 'required|numeric');
        $this->form_validation->set_rules('satuan', 'Satuan', 'required|trim');
        $this->form_validation->set_rules('kondisi', 'Kondisi', 'required|trim');
        $this->form_validation->set_rules('tgl_pengadaan', 'Tanggal Pengadaan', 'required|trim');
        $this->form_validation->set_rules('lokasi_penyimpanan', 'Lokasi Penyimpanan', 'required|trim');

        if ($this->form_validation->run() == FALSE) {
            $this->edit($id_barang);
        } else {
            // Array update TANPA menyertakan 'kode_barcode' agar nilainya di DB tetap permanen
            $data_update = [
                'nama_barang'        => $this->input->post('nama_barang', TRUE),
                'kategori'           => $this->input->post('kategori', TRUE),
                'stok'               => (int) $this->input->post('stok', TRUE),
                'satuan'             => $this->input->post('satuan', TRUE),
                'kondisi'            => $this->input->post('kondisi', TRUE),
                'tgl_pengadaan'      => $this->input->post('tgl_pengadaan', TRUE),
                'lokasi_penyimpanan' => $this->input->post('lokasi_penyimpanan', TRUE),
                'updated_at'         => date('Y-m-d H:i:s')
            ];

            $this->db->where('id_barang', $id_barang);
            $update = $this->db->update('tb_inventaris', $data_update);

            if ($update) {
                $this->session->set_flashdata('success', 'Data ' . strtolower($jenis) . ' berhasil diperbarui.');
            } else {
                $this->session->set_flashdata('error', 'Gagal memperbarui data.');
            }

            redirect('inventaris/' . strtolower($jenis));
        }
    }

    public function export_excel($jenis = 'Alat')
    {
        $role = strtolower($this->session->userdata('role') ?? '');

        if (!in_array($role, ['admin', 'laboran', 'kepsek'])) {
            $this->session->set_flashdata('error', 'Akses ditolak!');
            redirect('dashboard');
            return;
        }

        $jenis = (strtolower($jenis) === 'bahan') ? 'Bahan' : 'Alat';

        // 1. Ambil data inventaris
        $this->db->from('tb_inventaris');
        if ($this->db->field_exists('jenis', 'tb_inventaris')) {
            $this->db->where('jenis', $jenis);
        } else {
            if ($jenis === 'Bahan') {
                $this->db->like('kategori', 'Bahan');
            } else {
                $this->db->not_like('kategori', 'Bahan');
            }
        }
        $this->db->order_by('id_barang', 'DESC');
        $data['inventaris'] = $this->db->get()->result();

        // 2. Ambil data Kepsek & Laboran dari tb_users
        $data['kepsek']  = $this->db->get_where('tb_users', ['role' => 'kepsek'])->row();
        $data['laboran'] = $this->db->get_where('tb_users', ['role' => 'laboran'])->row();

        // 3. Wajib: Passing tgl_cetak dan nama_cetak ke Array $data
        $data['jenis']      = $jenis;
        $data['title']      = ($jenis === 'Bahan') ? 'LAPORAN STOK BAHAN HABIS PAKAI (BHP)' : 'LAPORAN STOK ALAT LABORATORIUM';
        $data['tgl_cetak']  = date('d/m/Y H:i:s');
        $data['nama_cetak'] = $this->session->userdata('nama_lengkap') ?? 'Operator Sekolah';

        // Header HTTP untuk Excel
        $filename = 'Laporan_Stok_' . $jenis . '_' . date('Ymd_His') . '.xls';

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Cache-Control: max-age=0");
        header("Pragma: no-cache");
        header("Expires: 0");

        $this->load->view('inventaris/v_excel', $data);
    }
}
