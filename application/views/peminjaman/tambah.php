<div class="p-6">
    <!-- Header Form -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Form Pinjam Alat Laboratorium</h2>
            <p class="text-xs text-slate-500">Isi formulir pengajuan peminjaman instrumen praktikum medis.</p>
        </div>
        <a href="<?= site_url('peminjaman'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
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

        <form action="<?= site_url('peminjaman/simpan'); ?>" method="POST" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- 1. Kode Peminjaman -->
                <div>
                    <label for="kode_peminjaman" class="block text-xs font-bold text-slate-700 mb-2">
                        KODE PEMINJAMAN <span class="text-slate-400 font-normal normal-case">(Otomatis)</span>
                    </label>
                    <div class="relative">
                        <input type="text" name="kode_peminjaman" id="kode_peminjaman" value="<?= 'PJM-' . date('Ymd') . '-' . rand(100, 999); ?>"
                            readonly class="w-full px-3.5 py-2.5 pr-10 text-xs bg-slate-100 border border-slate-200 rounded-xl font-mono font-semibold text-slate-500 cursor-not-allowed select-none focus:outline-none">
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                    </div>
                </div>

                <!-- 2. Select Instrument / Alat -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        PILIH INSTRUMEN / ALAT <span class="text-rose-500">*</span>
                    </label>
                    <select id="select_alat" name="id_barang" required onchange="updateKondisiAlat(this)" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800 cursor-pointer">
                        <option value="" data-kondisi="Baik">-- Pilih Alat --</option>
                        <?php if (!empty($alat_list)): ?>
                            <?php foreach ($alat_list as $a): ?>
                                <option value="<?= $a->id_barang; ?>" data-kondisi="<?= html_escape($a->kondisi ?? 'Baik'); ?>">
                                    <?= html_escape($a->nama_barang); ?> (Stok Tersedia: <?= $a->stok . ' ' . $a->satuan; ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 3. Kondisi Alat (DISABLED / READONLY) -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">KONDISI ALAT (DARI STOK)</label>
                    <!-- Display Input (Disabled agar tidak dapat diedit) -->
                    <input type="text" id="kondisi_display" value="Baik" disabled class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-700 cursor-not-allowed">

                    <!-- Hidden Input (Untuk mengirimkan data ke database POST) -->
                    <input type="hidden" id="kondisi_sebelum" name="kondisi_sebelum" value="Baik">
                </div>

                <!-- 4. Jumlah Pinjam -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        JUMLAH PINJAM <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" min="1" name="jumlah_pinjam" value="1" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                </div>

                <!-- 5. Tanggal Pinjam -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL PINJAM <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_pinjam" value="<?= date('Y-m-d'); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>

                <!-- 6. Rencana Kembali -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">RENCANA KEMBALI <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_kembali_rencana" value="<?= date('Y-m-d', strtotime('+1 day')); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <!-- Catatan Praktikum -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">CATATAN / KEPERLUAN PRAKTIKUM</label>
                <textarea name="catatan" rows="3" placeholder="Contoh: Praktikum Pemasangan Kateter Keperawatan" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800"></textarea>
            </div>

            <!-- Tombol Action -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('peminjaman'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Pengajuan Pinjaman
                </button>
            </div>
        </form>
    </div>
</div>

<!-- JavaScript untuk Update Kondisi Secara Otomatis -->
<script>
    function updateKondisiAlat(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const kondisi = selectedOption.getAttribute('data-kondisi') || 'Baik';

        // Set teks kondisi pada field disabled dan hidden input
        document.getElementById('kondisi_display').value = kondisi;
        document.getElementById('kondisi_sebelum').value = kondisi;
    }
</script>