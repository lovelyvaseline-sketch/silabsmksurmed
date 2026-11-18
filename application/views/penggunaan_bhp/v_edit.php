<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Edit Penggunaan BHP</h2>
            <p class="text-xs text-slate-500">Perbarui rincian pengajuan pemakaian bahan habis pakai.</p>
        </div>
        <a href="<?= site_url('penggunaan_bhp'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-2xl">
        <?php $error_message = $this->session->flashdata('error'); ?>
        <?php if ($error_message): ?>
            <div class="mb-5 p-4 text-xs text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                <?= html_escape($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (validation_errors()): ?>
            <div class="mb-5 p-4 text-xs text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                <?= validation_errors(); ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('penggunaan_bhp/update'); ?>" method="POST" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id_penggunaan" value="<?= $penggunaan->id_penggunaan; ?>">

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">KODE PENGGUNAAN</label>
                <input type="text" value="<?= $penggunaan->kode_penggunaan; ?>" readonly class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-700">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">PILIH BAHAN (BHP) <span class="text-rose-500">*</span></label>
                <select name="id_barang" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                    <option value="">-- Pilih Bahan --</option>
                    <?php if (!empty($bhp_list)): ?>
                        <?php foreach ($bhp_list as $b): ?>
                            <option value="<?= $b->id_barang; ?>" <?= ($b->id_barang == $penggunaan->id_barang) ? 'selected' : ''; ?>>
                                <?= html_escape($b->nama_barang); ?> (Stok: <?= $b->stok . ' ' . $b->satuan; ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">JUMLAH PAKAI <span class="text-rose-500">*</span></label>
                    <input type="number" min="1" name="jumlah_pakai" value="<?= $penggunaan->jumlah_pakai; ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL PENGGUNAAN <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_penggunaan" value="<?= date('Y-m-d', strtotime($penggunaan->tgl_penggunaan)); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">KETERANGAN / UNTUK PRAKTIKUM</label>
                <textarea name="keterangan" rows="3" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800"><?= html_escape($penggunaan->keterangan); ?></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('penggunaan_bhp'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>