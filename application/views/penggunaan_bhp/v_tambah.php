<div class="p-6">
    <!-- Header Form -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Form Penggunaan Bahan Habis Pakai (BHP)</h2>
            <p class="text-xs text-slate-500">Isi formulir pengajuan pemakaian bahan habis pakai laboratorium.</p>
        </div>
        <a href="<?= site_url('penggunaan_bhp'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">
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

        <form action="<?= site_url('penggunaan_bhp/simpan'); ?>" method="POST" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- 1. Kode Penggunaan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">KODE PENGGUNAAN</label>
                    <input type="text" name="kode_penggunaan" value="<?= 'BHP-' . date('Ymd') . '-' . rand(100, 999); ?>" readonly class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-700 cursor-not-allowed">
                </div>

                <!-- 2. Select Bahan Habis Pakai (BHP) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        PILIH BAHAN HABIS PAKAI (BHP) <span class="text-rose-500">*</span>
                    </label>
                    <select id="select_bhp" name="id_barang" required onchange="updateKondisiBhp(this)" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800 cursor-pointer">
                        <option value="" data-kondisi="">-- Pilih Bahan (BHP) --</option>
                        <?php if (!empty($bhp_list)): ?>
                            <?php foreach ($bhp_list as $b): ?>
                                <option value="<?= $b->id_barang; ?>" data-kondisi="<?= html_escape($b->kondisi ?? 'Baik'); ?>">
                                    <?= html_escape($b->nama_barang); ?> (Stok Tersedia: <?= $b->stok . ' ' . $b->satuan; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 3. Kondisi Awal BHP (Disabled) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">KONDISI AWAL BAHAN</label>
                    <input type="text" id="kondisi_display_bhp" value="Baik" disabled class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-600 cursor-not-allowed">
                </div>

                <!-- 4. Jumlah Pakai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        JUMLAH PAKAI <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" min="1" name="jumlah_pakai" value="1" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                </div>

                <!-- 5. Tanggal Penggunaan -->
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL PENGGUNAAN <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_penggunaan" value="<?= date('Y-m-d'); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <!-- Keterangan Praktikum -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">CATATAN / KEPERLUAN PRAKTIKUM</label>
                <textarea name="keterangan" rows="3" placeholder="Contoh: Praktikum Pembersihan Luka Keperawatan" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800"></textarea>
            </div>

            <!-- Tombol Action -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('penggunaan_bhp'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan BHP
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateKondisiBhp(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const kondisi = selectedOption.getAttribute('data-kondisi') || 'Baik';
        document.getElementById('kondisi_display_bhp').value = kondisi;
    }
</script>