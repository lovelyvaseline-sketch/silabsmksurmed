<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Detail Penggunaan BHP</h2>
            <p class="text-xs text-slate-500">Rincian informasi pemakaian bahan habis pakai laboratorium.</p>
        </div>
        <a href="<?= site_url('penggunaan_bhp'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Content Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-slate-700">
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Kode Penggunaan</label>
                <p class="font-semibold text-slate-800 text-base mt-1"><?= html_escape($penggunaan->kode_penggunaan); ?></p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Pemakai / Pengaju</label>
                <p class="font-medium text-slate-800 mt-1"><?= html_escape($penggunaan->pemakai ?? '-'); ?></p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Nama Bahan (BHP)</label>
                <p class="font-medium text-slate-800 mt-1"><?= html_escape($penggunaan->nama_barang); ?></p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Jumlah Pakai</label>
                <p class="font-bold text-teal-600 mt-1"><?= html_escape($penggunaan->jumlah_pakai . ' ' . $penggunaan->satuan); ?></p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Tanggal Penggunaan</label>
                <p class="font-medium text-slate-700 mt-1"><?= date('d/m/Y', strtotime($penggunaan->tgl_penggunaan)); ?></p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Status Pengajuan</label>
                <div class="mt-1">
                    <?php
                    $badge_class = 'bg-amber-50 text-amber-600 border-amber-200';
                    if ($penggunaan->status == 'Disetujui') $badge_class = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    elseif ($penggunaan->status == 'Ditolak') $badge_class = 'bg-rose-50 text-rose-600 border-rose-200';
                    ?>
                    <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold border <?= $badge_class; ?>">
                        <?= html_escape($penggunaan->status); ?>
                    </span>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="text-xs font-bold text-slate-400 uppercase">Keterangan / Keperluan</label>
                <p class="font-medium text-slate-700 mt-1"><?= html_escape($penggunaan->keterangan ?: '-'); ?></p>
            </div>
        </div>
    </div>
</div>