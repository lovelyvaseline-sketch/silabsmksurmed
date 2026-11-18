<div class="p-6">

    <!-- Header Form -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Edit Data Peminjaman</h2>
            <p class="text-xs text-slate-500">Perbarui detail pengajuan peminjaman yang masih Pending.</p>
        </div>
        <a href="<?= site_url('peminjaman'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Card Form Utama -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">

        <?php if (validation_errors()): ?>
            <div class="mb-6 p-4 text-xs text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                <div class="font-bold mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-exclamation"></i> Terjadi kesalahan pengisian form:
                </div>
                <div class="list-disc pl-5">
                    <?= validation_errors(); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php $error_message = $this->session->flashdata('error'); ?>
        <?php if ($error_message): ?>
            <div class="mb-5 p-4 text-xs text-rose-700 bg-rose-50 rounded-xl border border-rose-200">
                <?= html_escape($error_message); ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('peminjaman/update'); ?>" method="POST" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id_peminjaman" value="<?= is_object($peminjaman) ? $peminjaman->id_peminjaman : $peminjaman['id_peminjaman']; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- 1. Kode Peminjaman (Readonly) -->
                <div>
                    <label for="kode_peminjaman" class="block text-xs font-bold text-slate-700 mb-2">
                        KODE PEMINJAMAN <span class="text-slate-400 font-normal normal-case">(Tidak dapat diubah)</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="kode_peminjaman" id="kode_peminjaman"
                            value="<?= is_object($peminjaman) ? $peminjaman->kode_peminjaman : $peminjaman['kode_peminjaman']; ?>"
                            readonly class="w-full px-3.5 py-2.5 pr-10 text-xs bg-slate-100 border border-slate-200 rounded-xl font-mono font-semibold text-slate-500 cursor-not-allowed select-none focus:outline-none">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Status Peminjaman</label>
                    <div class="flex items-center h-[42px] px-3.5 bg-slate-100 border border-slate-200 rounded-xl">
                        <span class="text-xs text-amber-600 font-bold">Pending (Menunggu Pengesahan Laboran)</span>
                    </div>
                </div>

                <!-- Select Alat Medis / Instrumen -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Alat Medis / Instrumen <span class="text-rose-500">*</span>
                    </label>
                    <select name="id_barang" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800 cursor-pointer">
                        <option value="">-- Pilih Alat --</option>
                        <?php
                        $curr_id_barang = set_value('id_barang', is_object($peminjaman) ? $peminjaman->id_barang : $peminjaman['id_barang']);
                        ?>
                        <?php if (!empty($alat_list)): ?>
                            <?php foreach ($alat_list as $alat): ?>
                                <option value="<?= $alat->id_barang; ?>" <?= ($curr_id_barang == $alat->id_barang) ? 'selected' : ''; ?>>
                                    <?= html_escape($alat->kode_barcode . ' - ' . $alat->nama_barang); ?> (Stok: <?= $alat->stok . ' ' . $alat->satuan; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <!-- 4. Jumlah Pinjam -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Jumlah Pinjam <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" min="1" name="jumlah_pinjam" value="<?= set_value('jumlah_pinjam', is_object($peminjaman) ? ($peminjaman->jumlah_pinjam ?? $peminjaman->jumlah) : ($peminjaman['jumlah_pinjam'] ?? $peminjaman['jumlah'])); ?>" required
                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800 font-semibold">
                </div>

                <!-- 5. Tanggal Pinjam -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Tanggal Pinjam <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tgl_pinjam" value="<?= set_value('tgl_pinjam', date('Y-m-d', strtotime(is_object($peminjaman) ? $peminjaman->tgl_pinjam : $peminjaman['tgl_pinjam']))); ?>" required
                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800">
                </div>

                <!-- 6. Rencana Tanggal Kembali -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Rencana Tanggal Kembali <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tgl_kembali_rencana" value="<?= set_value('tgl_kembali_rencana', date('Y-m-d', strtotime(is_object($peminjaman) ? $peminjaman->tgl_kembali_rencana : $peminjaman['tgl_kembali_rencana']))); ?>" required
                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800">
                </div>

                <!-- 7. Catatan / Keperluan Praktikum -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Catatan / Keperluan Praktikum
                    </label>
                    <textarea name="catatan" rows="3" placeholder="Tuliskan catatan keperluan praktikum..."
                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800"><?= set_value('catatan', is_object($peminjaman) ? ($peminjaman->catatan ?? ($peminjaman->keterangan ?? '')) : ($peminjaman['catatan'] ?? ($peminjaman['keterangan'] ?? ''))); ?></textarea>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('peminjaman'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>