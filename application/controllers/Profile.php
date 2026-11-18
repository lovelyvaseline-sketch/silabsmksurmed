<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Profile extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->session->userdata('id_user')) {
            redirect('auth');
        }
    }

    // ==========================================
    // TAMPILKAN HALAMAN PROFIL
    // ==========================================
    public function index()
    {
        $id_user = $this->session->userdata('id_user');
        $user    = $this->db->get_where('tb_users', array('id_user' => $id_user))->row();

        $data['title'] = 'Profil Saya';
        $data['user']  = $user;

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('profile/v_index.php', $data); // atau 'users/profile' sesuai letak berkasmu
        $this->load->view('templates/footer');
    }

    // ==========================================
    // PROSES UPDATE PROFIL & FOTO
    // ==========================================
    public function update_profile()
    {
        $id_user = $this->session->userdata('id_user');
        $user    = $this->db->get_where('tb_users', array('id_user' => $id_user))->row();

        if (!$user) {
            redirect('auth');
            return;
        }

        $data_update = array(
            'nama_lengkap' => $this->input->post('nama_lengkap', TRUE),
            'email'        => $this->input->post('email', TRUE),
            'updated_at'   => date('Y-m-d H:i:s')
        );

        // Update Password jika diisi
        $password_baru = $this->input->post('password');
        if (!empty($password_baru)) {
            $data_update['password'] = password_hash($password_baru, PASSWORD_BCRYPT);
        }

        // Handle Upload / Ganti Foto Profil
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
                // Hapus foto lama jika ada di server
                if (!empty($user->foto) && file_exists(FCPATH . 'uploads/users/' . $user->foto)) {
                    unlink(FCPATH . 'uploads/users/' . $user->foto);
                }

                $foto_baru = $this->upload->data('file_name');
                $data_update['foto'] = $foto_baru;

                // UPDATE FOTO KE SESSION LOGIN SAAT INI
                $this->session->set_userdata('foto', $foto_baru);
            } else {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('profile');
                return;
            }
        }

        // Update Data ke Database
        $this->db->where('id_user', $id_user);
        $this->db->update('tb_users', $data_update);

        // Update Data Nama Lengkap ke Session
        $this->session->set_userdata('nama_lengkap', $data_update['nama_lengkap']);

        $this->session->set_flashdata('success', 'Profil dan foto akun berhasil diperbarui.');
        redirect('profile');
    }
}
