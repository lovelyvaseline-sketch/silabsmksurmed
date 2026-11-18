<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_Inventaris extends CI_Model
{

    // Ambil seluruh data inventaris
    public function get_all()
    {
        $this->db->order_by('id_barang', 'DESC');
        return $this->db->get('tb_inventaris')->result_array();
    }

    // Ambil data inventaris berdasarkan ID
    public function get_by_id($id)
    {
        return $this->db->get_where('tb_inventaris', ['id_barang' => $id])->row_array();
    }

    // Tambah data inventaris baru
    public function insert($data)
    {
        return $this->db->insert('tb_inventaris', $data);
    }

    // Update data inventaris
    public function update($id, $data)
    {
        $this->db->where('id_barang', $id);
        return $this->db->update('tb_inventaris', $data);
    }

    // Hapus data inventaris
    public function delete($id)
    {
        $this->db->where('id_barang', $id);
        return $this->db->delete('tb_inventaris');
    }

    // 1. Ambil semua data inventaris
    public function get_new_all()
    {
        return $this->db->get('tb_inventaris')->result();
    }

    // 2. Hitung total seluruh item inventaris (Method yang hilang)
    public function get_total_items()
    {
        return $this->db->count_all_results('tb_inventaris');
    }

    // 3. Hitung jumlah item berdasarkan kategori (misal: 'Bahan Habis Pakai' atau 'Alat Medis / Instrumen')
    public function count_by_kategori($kategori)
    {
        $this->db->where('kategori', $kategori);
        return $this->db->count_all_results('tb_inventaris');
    }

    public function get_stok_menipis()
    {
        $this->db->group_start()
            ->group_start()
                ->where('jenis', 'Alat')
                ->where('stok <=', 2)
            ->group_end()
            ->or_group_start()
                ->where('jenis', 'Bahan')
                ->where('stok <=', 5)
            ->group_end()
        ->group_end();
        $this->db->order_by('stok', 'ASC');

        return $this->db->get('tb_inventaris')->result();
    }

    // Kurangi stok alat saat disetujui / dipinjam
    public function kurangi_stok($id_barang, $jumlah, $jenis = NULL)
    {
        $jumlah = (int) $jumlah;
        if ($jumlah <= 0) {
            return FALSE;
        }

        $this->db->where('id_barang', $id_barang);
        $this->db->where('stok >=', $jumlah);
        if ($jenis !== NULL) {
            $this->db->where('jenis', $jenis);
            $this->db->where('kondisi', 'Baik');
        }
        $this->db->set('stok', 'stok - ' . $jumlah, FALSE);
        $update = $this->db->update('tb_inventaris');

        return $update && $this->db->affected_rows() === 1;
    }

    // Tambahkan kembali stok alat saat dikembalikan
    public function tambah_stok($id_barang, $jumlah)
    {
        $this->db->set('stok', 'stok + ' . (int)$jumlah, FALSE);
        $this->db->where('id_barang', $id_barang);
        return $this->db->update('tb_inventaris');
    }

    public function proses_approval($id_peminjaman, $status_tujuan)
    {
        // 1. Ambil data peminjaman saat ini
        $peminjaman = $this->db->get_where('tb_peminjaman', ['id_peminjaman' => $id_peminjaman])->row();

        if (!$peminjaman) {
            return false;
        }

        $status_lama   = $peminjaman->status;
        $id_barang     = $peminjaman->id_barang;
        $jumlah_pinjam = (int) ($peminjaman->jumlah_pinjam ?? $peminjaman->jumlah ?? 1);

        // Ambil data barang untuk cek ketersediaan stok
        $barang = $this->db->get_where('tb_inventaris', ['id_barang' => $id_barang])->row();

        // 2. Jika disetujui (Pending -> Dipinjam): Cek ketersediaan stok
        if ($status_lama === 'Pending' && $status_tujuan === 'Dipinjam') {
            if (!$barang || $barang->stok < $jumlah_pinjam) {
                return 'stok_habis';
            }
        }

        // Mulai Transaksi Database (ACID Protection)
        $this->db->trans_start();

        // A. Pemotongan Stok (Pending -> Dipinjam)
        if ($status_lama === 'Pending' && $status_tujuan === 'Dipinjam') {
            $this->db->set('stok', 'stok - ' . $jumlah_pinjam, FALSE);
            $this->db->where('id_barang', $id_barang);
            $this->db->update('tb_inventaris');
        }

        // B. Penambahan Stok Kembali (Dipinjam -> Dikembalikan)
        elseif ($status_lama === 'Dipinjam' && $status_tujuan === 'Dikembalikan') {
            $this->db->set('stok', 'stok + ' . $jumlah_pinjam, FALSE);
            $this->db->where('id_barang', $id_barang);
            $this->db->update('tb_inventaris');
        }

        // C. Update Status Peminjaman
        $this->db->where('id_peminjaman', $id_peminjaman);
        $this->db->update('tb_peminjaman', [
            'status'     => $status_tujuan,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
