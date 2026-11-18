<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Cek Kondisi & Pengembalian Alat</h2>
            <p class="text-xs text-slate-500">Pemeriksaan kondisi instrumen setelah digunakan oleh praktikan.</p>
        </div>
        <a href="<?= site_url('peminjaman'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-2xl">
        <div class="p-4 bg-slate-50 border border-slate-200 rounded-xl mb-6 grid grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-slate-400 font-bold block">KODE PEMINJAMAN</span>
                <span class="font-bold text-slate-800"><?= $peminjaman->kode_peminjaman; ?></span>
            </div>
            <div>
                <span class="text-slate-400 font-bold block">PEMINJAM</span>
                <span class="font-bold text-slate-800"><?= html_escape($peminjaman->peminjam); ?></span>
            </div>
            <div>
                <span class="text-slate-400 font-bold block">NAMA ALAT</span>
                <span class="font-bold text-teal-600"><?= html_escape($peminjaman->nama_barang); ?></span>
            </div>
            <div>
                <span class="text-slate-400 font-bold block">KONDISI SEBELUM (BEFORE)</span>
                <span class="font-bold text-slate-700"><?= $peminjaman->kondisi_sebelum; ?></span>
            </div>
        </div>

        <form action="<?= site_url('peminjaman/proses_pengembalian'); ?>" method="POST" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id_peminjaman" value="<?= $peminjaman->id_peminjaman; ?>">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    KONDISI ALAT SETELAH DIPINJAM (AFTER) <span class="text-rose-500">*</span>
                </label>
                <select name="kondisi_sesudah" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800 cursor-pointer">
                    <option value="Baik" selected>Baik (Tidak Ada Kerusakan)</option>
                    <option value="Rusak Ringan">Rusak Ringan</option>
                    <option value="Rusak Berat">Rusak Berat</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    CATATAN LABORAN / DESKRIPSI KERUSAKAN
                </label>
                <textarea name="catatan_laboran" rows="3" placeholder="Tuliskan catatan fisik alat atau detail kerusakan jika ada..." class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('peminjaman'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-square-check"></i> Konfirmasi Pengembalian
                </button>
            </div>
        </form>
    </div>
</div>