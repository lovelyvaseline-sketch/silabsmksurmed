<div class="p-6">
    <?php $role_user = strtolower($this->session->userdata('role') ?? ''); ?>

    <!-- Header Form -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Edit Pengajuan Pengadaan Alat & BHP</h2>
            <p class="text-xs text-slate-500">Perbarui rincian item atau estimasi kebutuhan sebelum diproses oleh Laboran & Kepsek.</p>
        </div>
        <a href="<?= site_url('pengadaan'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Card Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">
        <form action="<?= site_url('pengadaan/proses_edit'); ?>" method="POST" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id_pengadaan" value="<?= $pengadaan->id_pengadaan; ?>">

            <!-- Banner Info Pengaju & ID -->
            <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl flex justify-between items-center">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">PENGAJU USULAN</span>
                    <span class="text-sm font-bold text-slate-800"><?= html_escape($pengadaan->nama_pengaju ?? 'Guru / Laboran'); ?></span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">ID PENGADAAN</span>
                    <span class="text-sm font-bold text-teal-600">#<?= str_pad($pengadaan->id_pengadaan, 4, '0', STR_PAD_LEFT); ?></span>
                </div>
            </div>

            <!-- Nama Item / Barang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">NAMA ITEM / BARANG <span class="text-rose-500">*</span></label>
                <input type="text" name="nama_item" value="<?= html_escape($pengadaan->nama_item); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Jumlah -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">JUMLAH <span class="text-rose-500">*</span></label>
                    <input type="number" min="1" name="jumlah" value="<?= $pengadaan->jumlah; ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                </div>

                <!-- Satuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">SATUAN <span class="text-rose-500">*</span></label>
                    <input type="text" name="satuan" value="<?= html_escape($pengadaan->satuan); ?>" required placeholder="Contoh: Box, Pcs, Botol, Pack" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Estimasi Harga Total -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        ESTIMASI HARGA TOTAL (RP) <span class="text-rose-500">*</span>
                    </label>

                    <!-- Input Tampilan (Teks berformat Rp 25.000) -->
                    <input type="text"
                        id="harga_display"
                        value="<?= 'Rp ' . number_format($pengadaan->estimasi_harga ?? 0, 0, ',', '.'); ?>"
                        onkeyup="formatRupiahInput(this)"
                        required
                        class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800"
                        placeholder="Rp 0">

                    <!-- Input Hidden (Angka Murni 25000 tanpa titik/Rp untuk dikirim ke Controller/DB) -->
                    <input type="hidden"
                        id="estimasi_harga"
                        name="estimasi_harga"
                        value="<?= (int) ($pengadaan->estimasi_harga ?? 0); ?>">
                </div>

                <!-- Status Persetujuan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">STATUS PERSETUJUAN</label>
                    <input type="text" value="<?= html_escape($pengadaan->status_approval ?? 'Pending'); ?>" disabled class="w-full px-3.5 py-2.5 text-xs bg-slate-100 border border-slate-200 rounded-xl font-bold text-slate-600 cursor-not-allowed">
                </div>
            </div>

            <!-- Alasan / Keterangan Pengadaan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">ALASAN / KETERANGAN PENGADAAN</label>
                <textarea name="alasan_pengadaan" rows="3" placeholder="Tuliskan alasan urgensi pengadaan..." class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800"><?= html_escape($pengadaan->alasan_pengadaan ?? ''); ?></textarea>
            </div>

            <!-- Tombol Action -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('pengadaan'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui Data Pengadaan
                </button>
            </div>
        </form>
    </div>
</div>


<script>
    function formatRupiahInput(input) {
        // 1. Ambil hanya karakter angka dari input
        let numberString = input.value.replace(/[^,\d]/g, '').toString();
        let split = numberString.split(',');
        let sisa = split[0].length % 3;
        let rupiah = split[0].substr(0, sisa);
        let ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        // 2. Format dengan tanda titik setiap 3 digit ribuan
        if (ribuan) {
            let separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] !== undefined ? rupiah + ',' + split[1] : rupiah;

        // 3. Tampilkan text 'Rp ' + angka berformat ke input display
        input.value = numberString ? 'Rp ' + rupiah : '';

        // 4. Set angka murni (tanpa 'Rp' dan titik) ke input hidden untuk dikirim ke backend
        document.getElementById('estimasi_harga').value = numberString ? split[0] : 0;
    }
</script>