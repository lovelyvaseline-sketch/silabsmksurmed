<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Admin extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        // Keamanan: Cek apakah user sudah login dan ber-role laboran / admin / kepsek
        $role = $this->session->userdata('role');
        if (!in_array($role, ['laboran', 'admin', 'kepsek'])) {
            $this->session->set_flashdata('error', 'Akses terbatas! Halaman ini khusus Admin.');
            redirect(site_url('pengadaan'));
        }
    }

    // Dashboard persetujuan pengadaan Admin
    public function index()
    {
        $data['title'] = 'Persetujuan Pengadaan - Admin';

        // Hitung Ringkasan
        $data['total_pengajuan'] = $this->db->count_all('tb_pengadaan');
        $data['total_pending']   = $this->db->where('status_approval', 'Pending')->count_all_results('tb_pengadaan');
        $data['total_disetujui'] = $this->db->where('status_approval', 'Disetujui Kepsek')->count_all_results('tb_pengadaan');
        $data['total_ditolak']   = $this->db->where('status_approval', 'Ditolak')->count_all_results('tb_pengadaan');

        // Total Estimasi Biaya yang Disetujui
        $this->db->select_sum('estimasi_harga');
        $this->db->where('status_approval', 'Disetujui Kepsek');
        $query_biaya = $this->db->get('tb_pengadaan')->row();
        $data['total_biaya_disetujui'] = $query_biaya->estimasi_harga ?? 0;

        // Ambil Semua Daftar Pengadaan
        $this->db->from('tb_pengadaan');
        $this->db->order_by('id_pengadaan', 'DESC');
        $data['pengadaan'] = $this->db->get()->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('admin/index', $data);
        $this->load->view('templates/footer');
    }

    // Aksi Cepat Persetujuan (Approve / Reject dari Tabel)
    public function persetujuan_cepat($id = NULL)
    {
        // Aksi perubahan data hanya melalui formulir POST.
        if ($this->input->method() !== 'post') {
            header('Allow: POST');
            show_error('Gunakan formulir untuk menjalankan aksi ini.', 405);
            return;
        }

        $status = $this->input->post('status', TRUE);
        $role = $this->session->userdata('role');
        $pengadaan = $this->db->get_where('tb_pengadaan', [
            'id_pengadaan' => $id
        ])->row();

        if (!$pengadaan || !in_array($status, ['Disetujui', 'Ditolak'], TRUE)) {
            $this->session->set_flashdata('error', 'Status tidak valid!');
            redirect(site_url('admin'));
            return;
        }

        $status_sebelumnya = $pengadaan->status_approval;
        $status_baru = NULL;
        $boleh_proses = FALSE;

        if ($status_sebelumnya === 'Pending' && in_array($role, ['laboran', 'admin'], TRUE)) {
            $status_baru = ($status === 'Disetujui') ? 'Disetujui Laboran' : 'Ditolak';
            $boleh_proses = TRUE;
        } elseif ($status_sebelumnya === 'Disetujui Laboran' && in_array($role, ['kepsek', 'admin'], TRUE)) {
            $status_baru = ($status === 'Disetujui') ? 'Disetujui Kepsek' : 'Ditolak';
            $boleh_proses = TRUE;
        }

        if (!$boleh_proses) {
            $this->session->set_flashdata('error', 'Status pengadaan atau role tidak sesuai dengan tahap approval.');
            redirect(site_url('admin'));
            return;
        }

        $this->db->where('id_pengadaan', $id);
        $this->db->where('status_approval', $status_sebelumnya);
        $updated = $this->db->update('tb_pengadaan', [
            'status_approval' => $status_baru
        ]);

        if (!$updated || $this->db->affected_rows() !== 1) {
            $this->session->set_flashdata('error', 'Status pengadaan gagal diperbarui.');
            redirect(site_url('admin'));
            return;
        }

        $this->session->set_flashdata('success', 'Status pengadaan berhasil diubah menjadi: ' . $status_baru);
        redirect(site_url('admin'));
    }
}
