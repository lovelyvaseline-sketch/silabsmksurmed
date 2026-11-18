<div class="p-6">
    <!-- Header Form -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800"><?= $title; ?></h2>
            <p class="text-xs text-slate-500">Perbarui rincian informasi data stok <?= strtolower($jenis); ?> laboratorium.</p>
        </div>
        <a href="<?= site_url('inventaris/' . strtolower($jenis)); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Card Form Utama -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">

        <!-- Pesan Error Validasi CI3 -->
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

        <form action="<?= site_url('inventaris/update'); ?>" method="POST" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <!-- Hidden Keys -->
            <input type="hidden" name="id_barang" value="<?= $barang->id_barang; ?>">
            <input type="hidden" name="jenis" value="<?= $jenis; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                <!-- INPUT KODE BARCODE (TERKUNCI SAAT EDIT) -->
                <div class="mb-4">
                    <label for="kode_barcode" class="block text-xs font-bold text-slate-700 uppercase mb-1.5">
                        KODE BARANG <span class="text-slate-400 font-normal normal-case">(Tidak dapat diubah)</span>
                    </label>
                    <div class="relative">
                        <input type="text"
                            name="kode_barcode"
                            id="kode_barcode"
                            value="<?= html_escape($barang->kode_barcode); ?>"
                            readonly
                            class="w-full px-4 py-2.5 text-sm font-mono font-semibold text-slate-500 bg-slate-100 border border-slate-200 rounded-xl cursor-not-allowed select-none focus:outline-none">

                        <!-- Ikon Gembok sebagai penanda terkunci -->
                        <span class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-slate-400">
                            <i class="fa-solid fa-lock text-xs"></i>
                        </span>
                    </div>
                </div>
                <!-- 2. Nama Barang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        NAMA <?= strtoupper($jenis); ?> <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_barang" value="<?= set_value('nama_barang', $barang->nama_barang); ?>"
                        required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800">
                </div>

                <!-- 3. Kategori -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        KATEGORI <span class="text-rose-500">*</span>
                    </label>
                    <select name="kategori" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-700 cursor-pointer">
                        <option value="">-- Pilih Kategori --</option>
                        <?php
                        $curr_kategori = set_value('kategori', $barang->kategori);
                        ?>
                        <?php if ($jenis === 'Bahan'): ?>
                            <option value="Bahan Habis Pakai Medis" <?= ($curr_kategori == 'Bahan Habis Pakai Medis') ? 'selected' : ''; ?>>Bahan Habis Pakai Medis</option>
                            <option value="Cairan / Desinfektan" <?= ($curr_kategori == 'Cairan / Desinfektan') ? 'selected' : ''; ?>>Cairan / Desinfektan</option>
                            <option value="Obat & Praktikum" <?= ($curr_kategori == 'Obat & Praktikum') ? 'selected' : ''; ?>>Obat & Praktikum</option>
                            <option value="Perlengkapan Lab" <?= ($curr_kategori == 'Perlengkapan Lab') ? 'selected' : ''; ?>>Perlengkapan Lab</option>
                        <?php else: ?>
                            <option value="Alat Medis / Instrumen" <?= ($curr_kategori == 'Alat Medis / Instrumen') ? 'selected' : ''; ?>>Alat Medis / Instrumen</option>
                            <option value="Manekin & Simulator" <?= ($curr_kategori == 'Manekin & Simulator') ? 'selected' : ''; ?>>Manekin & Simulator</option>
                            <option value="Perlengkapan Lab" <?= ($curr_kategori == 'Perlengkapan Lab') ? 'selected' : ''; ?>>Perlengkapan Lab</option>
                        <?php endif; ?>
                    </select>
                </div>

                <!-- 4. Kondisi -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        KONDISI <span class="text-rose-500">*</span>
                    </label>
                    <?php $curr_kondisi = set_value('kondisi', $barang->kondisi); ?>
                    <select name="kondisi" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-700 cursor-pointer">
                        <option value="Baik" <?= ($curr_kondisi == 'Baik') ? 'selected' : ''; ?>>Baik</option>
                        <option value="Rusak Ringan" <?= ($curr_kondisi == 'Rusak Ringan') ? 'selected' : ''; ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?= ($curr_kondisi == 'Rusak Berat') ? 'selected' : ''; ?>>Rusak Berat</option>
                    </select>
                </div>

                <!-- 5. Stok -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        JUMLAH STOK <span class="text-rose-500">*</span>
                    </label>
                    <input type="number" min="0" name="stok" value="<?= set_value('stok', $barang->stok); ?>" required
                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800 font-semibold">
                </div>

                <!-- 6. Satuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        SATUAN <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="satuan" value="<?= set_value('satuan', $barang->satuan); ?>" required
                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800">
                </div>
            </div>
            <!-- INPUT TANGGAL PENGADAAN -->
            <div class="mb-4">
                <label for="tgl_pengadaan" class="block text-xs font-bold text-slate-700 uppercase mb-2">
                    Tanggal Pengadaan <span class="text-rose-500">*</span>
                </label>
                <input type="date" name="tgl_pengadaan" id="tgl_pengadaan"
                    value="<?= date('Y-m-d'); ?>" required
                    class="w-full px-4 py-2.5 text-sm bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-teal-500 focus:bg-white transition">
                <p class="text-[11px] text-slate-400 mt-1">Tanggal barang dibeli/diterima secara resmi di laboratorium.</p>
            </div>

            <!-- 7. Lokasi Penyimpanan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">
                    LOKASI PENYIMPANAN / RAK <span class="text-rose-500">*</span>
                </label>
                <input type="text" name="lokasi_penyimpanan" value="<?= set_value('lokasi_penyimpanan', $barang->lokasi_penyimpanan); ?>" required
                    class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-800">
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('inventaris/' . strtolower($jenis)); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl transition shadow-sm flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui Data <?= $jenis; ?>
                </button>
            </div>
        </form>
    </div>
</div>