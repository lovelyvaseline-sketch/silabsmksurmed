<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Peminjaman extends CI_Model
{

    public function get_all_with_relations()
    {
        $this->db->select('p.*, u.nama_lengkap as peminjam, u.role, b.nama_barang, b.kode_barcode');
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user');
        $this->db->join('tb_inventaris b', 'b.id_barang = p.id_barang');
        $this->db->order_by('p.id_peminjaman', 'DESC');
        return $this->db->get()->result();
    }

    public function get_active_loans()
    {
        $this->db->select('p.*, u.nama_lengkap, b.nama_barang');
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user');
        $this->db->join('tb_inventaris b', 'b.id_barang = p.id_barang');

        // Gunakan 'p.status' agar tidak bentrok dengan kolom status di tabel lain
        $this->db->where('p.status', 'Dipinjam');

        return $this->db->count_all_results();
    }

    public function get_pending_loans()
    {
        $this->db->where('status', 'Pending');
        return $this->db->count_all_results('tb_peminjaman');
    }

    public function get_by_id($id_peminjaman)
    {
        return $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
    }

    public function insert($data)
    {
        return $this->db->insert('tb_peminjaman', $data);
    }

    public function update_status($id_peminjaman, $status, $tgl_kembali = NULL)
    {
        $this->db->where('id_peminjaman', $id_peminjaman);
        return $this->db->update('tb_peminjaman', [
            'status' => $status,
            'tgl_kembali' => $tgl_kembali
        ]);
    }

    // Hitung peminjaman milik user tertentu berdasarkan status
    public function count_user_loans($id_user, $status)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('status', $status);
        return $this->db->count_all_results('tb_peminjaman');
    }

    // Hitung total seluruh riwayat peminjaman user tertentu
    public function count_user_loans_total($id_user)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->count_all_results('tb_peminjaman');
    }


    //==========================================================================
    // 1. Ambil semua data peminjaman beserta relasi user & barang
    public function get_all_with_relation()
    {
        $this->db->select('p.*, u.nama_lengkap as peminjam, u.role, b.nama_barang, b.kode_barcode');
        $this->db->from('tb_peminjaman p');
        $this->db->join('tb_users u', 'u.id_user = p.id_user', 'left');
        $this->db->join('tb_inventaris b', 'b.id_barang = p.id_barang', 'left');
        $this->db->order_by('p.id_peminjaman', 'DESC');
        return $this->db->get()->result();
    }

    // 2. Hitung jumlah peminjaman aktif (status Dipinjam)
    public function get_active_loan()
    {
        $this->db->where('status', 'Dipinjam');
        return $this->db->count_all_results('tb_peminjaman');
    }

    // 3. Hitung jumlah peminjaman pending
    public function get_pending_loan()
    {
        $this->db->where('status', 'Pending');
        return $this->db->count_all_results('tb_peminjaman');
    }

    // 4. Ambil data peminjaman berdasarkan ID
    public function get_by_id_peminjaman($id_peminjaman)
    {
        return $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
    }

    // 5. Tambah transaksi peminjaman baru
    public function inserts($data)
    {
        return $this->db->insert('tb_peminjaman', $data);
    }

    // 6. Update status peminjaman (Approval / Pengembalian)
    public function update_status_peminjaman($id_peminjaman, $status, $tgl_kembali = NULL)
    {
        $this->db->where('id_peminjaman', $id_peminjaman);
        return $this->db->update('tb_peminjaman', [
            'status' => $status,
            'tgl_kembali_realisasi' => $tgl_kembali
        ]);
    }

    // 7. Hitung peminjaman spesifik per user berdasarkan status
    public function count_user_loans_status($id_user, $status)
    {
        $this->db->where('id_user', $id_user);
        $this->db->where('status', $status);
        return $this->db->count_all_results('tb_peminjaman');
    }

    // 8. Hitung total seluruh riwayat peminjaman user tertentu
    public function count_user_loans_total_peminjaman($id_user)
    {
        $this->db->where('id_user', $id_user);
        return $this->db->count_all_results('tb_peminjaman');
    }

    //================ STATUS APPROVAL PEMINJAMAN SISWA ========================

    // Update status transaksi sekaligus sinkronisasi stok inventaris
    public function proses_approval($id_peminjaman, $status_baru)
    {
        if (!in_array($status_baru, ['Dipinjam', 'Ditolak'], TRUE)) {
            return 'status_tidak_valid';
        }

        $peminjaman = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();
        if (!$peminjaman) {
            return FALSE;
        }
        if ($peminjaman->status !== 'Pending') {
            return 'sudah_diproses';
        }

        $jumlah_pinjam = (int) $peminjaman->jumlah_pinjam;
        if ($jumlah_pinjam <= 0) {
            return FALSE;
        }

        $this->load->model('M_Inventaris');
        $db_debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;

        try {
            if (!$this->db->trans_begin()) {
                return FALSE;
            }

            $this->db->where('id_peminjaman', $id_peminjaman);
            $this->db->where('status', 'Pending');
            $this->db->where('id_barang', $peminjaman->id_barang);
            $this->db->where('jumlah_pinjam', $jumlah_pinjam);
            $update = $this->db->update('tb_peminjaman', [
                'status' => $status_baru,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if (!$update || $this->db->affected_rows() !== 1) {
                $this->db->trans_rollback();
                return $update ? 'sudah_diproses' : FALSE;
            }

            if ($status_baru === 'Dipinjam') {
                $stok = $this->M_Inventaris->kurangi_stok($peminjaman->id_barang, $jumlah_pinjam, 'Alat');
                if (!$stok) {
                    $hasil = $this->db->trans_status() ? 'stok_habis' : FALSE;
                    $this->db->trans_rollback();
                    return $hasil;
                }
            }

            if ($this->db->trans_status() === FALSE || !$this->db->trans_commit()) {
                $this->db->trans_rollback();
                return FALSE;
            }
            return TRUE;
        } catch (Throwable $e) {
            $this->db->trans_rollback();
            log_message('error', 'Gagal memproses persetujuan peminjaman: ' . $e->getMessage());
            return FALSE;
        } finally {
            $this->db->db_debug = $db_debug;
        }
    }

        // Kembalikan alat melalui satu alur untuk controller peminjaman dan BHP.
    // Satu alur pengembalian untuk controller peminjaman dan BHP.
    public function kembalikan_aman($id, $kondisi, $catatan, $id_laboran)
    {
        if ((int) $id <= 0 || (int) $id_laboran <= 0
            || !in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'], TRUE)
            || !is_string($catatan)) {
            return FALSE;
        }

        $debug = $this->db->db_debug;
        $this->db->db_debug = FALSE;
        $hasil = FALSE;

        if ($this->db->trans_start()) {
            $ok = FALSE;
            $query = $this->db->get_where('tb_peminjaman', [
                'id_peminjaman' => (int) $id
            ]);
            $pinjam = $query ? $query->row() : NULL;

            if ($pinjam && $pinjam->status !== 'Dipinjam') {
                $hasil = 'sudah_diproses';
            } elseif ($pinjam && (int) $pinjam->jumlah_pinjam > 0) {
                $jumlah = (int) $pinjam->jumlah_pinjam;
                $barang = $this->db->get_where('tb_inventaris', [
                    'id_barang' => $pinjam->id_barang,
                    'jenis' => 'Alat'
                ]);

                if ($barang && $barang->row()) {
                    $waktu = date('Y-m-d H:i:s');
                    $update = $this->db
                        ->where('id_peminjaman', $pinjam->id_peminjaman)
                        ->where('status', 'Dipinjam')
                        ->where('id_barang', $pinjam->id_barang)
                        ->where('jumlah_pinjam', $jumlah)
                        ->update('tb_peminjaman', [
                            'status' => 'Dikembalikan',
                            'kondisi_sesudah' => $kondisi,
                            'catatan_laboran' => $catatan,
                            'tgl_kembali_realisasi' => $waktu,
                            'updated_at' => $waktu
                        ]);
                    $ok = $update && $this->db->affected_rows() === 1;

                    if ($ok && $kondisi === 'Baik') {
                        $this->load->model('M_Inventaris');
                        $stok = $this->M_Inventaris->tambah_stok(
                            $pinjam->id_barang, $jumlah
                        );
                        $ok = $stok && $this->db->affected_rows() === 1;
                    } elseif ($ok) {
                        $ok = $this->db->insert('tb_kerusakan_alat', [
                            'id_peminjaman' => $pinjam->id_peminjaman,
                            'id_barang' => $pinjam->id_barang,
                            'id_laboran' => (int) $id_laboran,
                            'kondisi_kerusakan' => $kondisi,
                            'jumlah_rusak' => $jumlah,
                            'status_perbaikan' => 'Menunggu Perbaikan',
                            'deskripsi_kerusakan' => $catatan !== ''
                                ? $catatan : 'Alat dikembalikan dalam keadaan rusak.',
                            'tgl_laporan' => $waktu
                        ]);
                    }
                }
            }

            if ($ok) {
                $hasil = $this->db->trans_complete();
                if (!$hasil) {
                    $this->db->trans_rollback();
                }
            } else {
                $this->db->trans_rollback();
            }
        }

        $this->db->db_debug = $debug;
        return $hasil;
    }

}
