<div class="p-6">
    <?php
    $role_user     = strtolower($this->session->userdata('role') ?? '');
    $id_user_login = $this->session->userdata('id_user');
    $status        = $pengadaan->status_approval ?? 'Pending';
    ?>

    <!-- Header Halaman -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Detail Pengadaan Alat & BHP</h2>
            <p class="text-xs text-slate-500">Informasi lengkap rincian barang dan status pelacakan approval 2 tingkat.</p>
        </div>
        <a href="<?= site_url('pengadaan'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Alert Flashdata -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="mb-4 p-4 text-sm text-emerald-700 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span><?= $this->session->flashdata('success'); ?></span>
        </div>
    <?php elseif ($this->session->flashdata('error')): ?>
        <div class="mb-4 p-4 text-sm text-rose-700 bg-rose-50 rounded-xl border border-rose-200 flex items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <span><?= $this->session->flashdata('error'); ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KOLOM KIRI (2/3): Rincian Pengajuan Pengadaan -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6 space-y-6">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">KODE / ID PENGAJUAN</span>
                    <h3 class="text-lg font-bold text-teal-600">#<?= str_pad($pengadaan->id_pengadaan, 4, '0', STR_PAD_LEFT); ?></h3>
                </div>
                <div>
                    <?php
                    $badge_class = 'bg-amber-50 text-amber-600 border-amber-200';
                    if ($status === 'Disetujui Laboran') $badge_class = 'bg-sky-50 text-sky-600 border-sky-200';
                    elseif ($status === 'Disetujui Kepsek') $badge_class = 'bg-indigo-50 text-indigo-600 border-indigo-200';
                    elseif ($status === 'Selesai') $badge_class = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    elseif ($status === 'Ditolak') $badge_class = 'bg-rose-50 text-rose-600 border-rose-200';
                    ?>
                    <span class="px-3.5 py-1.5 text-xs font-bold rounded-full border <?= $badge_class; ?>">
                        <?= html_escape($status); ?>
                    </span>
                </div>
            </div>

            <!-- Detail Item -->
            <div>
                <span class="text-xs font-semibold text-slate-400 block mb-1">NAMA ITEM / BARANG</span>
                <h4 class="text-xl font-bold text-slate-800"><?= html_escape($pengadaan->nama_item); ?></h4>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-slate-50/70 p-4 rounded-xl border border-slate-100">
                <div>
                    <span class="text-xs font-semibold text-slate-400 block mb-1">JUMLAH KEBUTUHAN</span>
                    <span class="text-base font-bold text-teal-600"><?= $pengadaan->jumlah . ' ' . html_escape($pengadaan->satuan ?? 'Pcs'); ?></span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 block mb-1">ESTIMASI HARGA TOTAL</span>
                    <span class="text-base font-bold text-slate-800">Rp <?= number_format($pengadaan->estimasi_harga ?? 0, 0, ',', '.'); ?></span>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="text-xs font-semibold text-slate-400 block mb-1">PENGAJU USULAN</span>
                    <span class="text-sm font-bold text-slate-700"><?= html_escape($pengadaan->nama_pengaju ?? 'Administrator'); ?></span>
                    <span class="text-xs text-slate-400 block font-medium uppercase">(<?= html_escape($pengadaan->role_pengaju ?? 'User'); ?>)</span>
                </div>
                <div>
                    <span class="text-xs font-semibold text-slate-400 block mb-1">TANGGAL DIAJUKAN</span>
                    <span class="text-sm font-semibold text-slate-700">
                        <?= !empty($pengadaan->created_at) ? date('d F Y - H:i', strtotime($pengadaan->created_at)) : '-'; ?>
                    </span>
                </div>
            </div>

            <!-- Alasan Pengadaan -->
            <div>
                <span class="text-xs font-semibold text-slate-400 block mb-2">ALASAN / KETERANGAN PENGADAAN</span>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-xs text-slate-700 leading-relaxed italic">
                    <?= !empty($pengadaan->alasan_pengadaan) ? nl2br(html_escape($pengadaan->alasan_pengadaan)) : 'Tidak ada keterangan tambahan yang dilampirkan.'; ?>
                </div>
            </div>

            <!-- Tombol Eksekusi Status Berdasarkan Role -->
            <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-end gap-3">

                <!-- 1. Laboran Verifikasi -->
                <?php if (in_array($role_user, array('laboran', 'admin')) && $status === 'Pending'): ?>
                    <form action="<?= site_url('pengadaan/update_status/' . $pengadaan->id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Verifikasi pengajuan ini dan teruskan ke Kepala Sekolah?')">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="status_baru" value="Disetujui Laboran">
                        <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white text-xs px-4 py-2.5 rounded-xl font-semibold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-check"></i> Verifikasi Laboran
                        </button>
                    </form>
                    <form action="<?= site_url('pengadaan/update_status/' . $pengadaan->id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Tolak pengajuan ini?')">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="status_baru" value="Ditolak Laboran">
                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-xs px-4 py-2.5 rounded-xl font-semibold transition">
                            Tolak Pengajuan
                        </button>
                    </form>
                <?php endif; ?>

                <!-- 2. Kepala Sekolah ACC -->
                <?php if (in_array($role_user, array('kepsek', 'admin')) && $status === 'Disetujui Laboran'): ?>
                    <form action="<?= site_url('pengadaan/update_status/' . $pengadaan->id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('ACC dan setujui pengadaan barang ini?')">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="status_baru" value="Disetujui Kepsek">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-4 py-2.5 rounded-xl font-semibold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-signature"></i> ACC Kepala Sekolah
                        </button>
                    </form>
                    <form action="<?= site_url('pengadaan/update_status/' . $pengadaan->id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Tolak pengajuan ini?')">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="status_baru" value="Ditolak Kepsek">
                        <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-xs px-4 py-2.5 rounded-xl font-semibold transition">
                            Tolak Pengajuan
                        </button>
                    </form>
                <?php endif; ?>

                <!-- 3. Barang Diterima -->
                <?php if (in_array($role_user, array('admin', 'laboran')) && $status === 'Disetujui Kepsek'): ?>
                    <form action="<?= site_url('pengadaan/update_status/' . $pengadaan->id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Tandai barang telah sampai di laboratorium?')">
                        <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                        <input type="hidden" name="status_baru" value="Selesai">
                        <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-4 py-2.5 rounded-xl font-semibold transition flex items-center gap-1.5 shadow-sm">
                            <i class="fa-solid fa-box-open"></i> Tandai Barang Diterima
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>

        <!-- KOLOM KANAN (1/3): Visual Progress Tracker (Stepper) -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-800 mb-1">Alur Approval Pengadaan</h3>
                <p class="text-xs text-slate-400 mb-6">Status progres persetujuan bertingkat pengadaan barang.</p>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-100">

                    <!-- STEP 1: Pengajuan Guru / Pengaju -->
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-emerald-50 text-white flex items-center justify-center text-[8px]">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <h4 class="text-xs font-bold text-slate-800">1. Pengajuan Usulan</h4>
                        <p class="text-[11px] text-slate-400">Pengajuan telah dibuat oleh pengaju.</p>
                    </div>

                    <!-- STEP 2: Verifikasi Laboran -->
                    <?php
                    $is_lab_pass = in_array($status, array('Disetujui Laboran', 'Disetujui Kepsek', 'Selesai'));
                    $is_lab_fail = ($status === 'Ditolak');
                    ?>
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full <?= $is_lab_pass ? 'bg-emerald-500 ring-emerald-50' : ($is_lab_fail ? 'bg-rose-500 ring-rose-50' : 'bg-slate-300 ring-slate-100'); ?> ring-4 text-white flex items-center justify-center text-[8px]">
                            <i class="fa-solid <?= $is_lab_pass ? 'fa-check' : ($is_lab_fail ? 'fa-xmark' : 'fa-clock'); ?>"></i>
                        </div>
                        <h4 class="text-xs font-bold <?= $is_lab_pass ? 'text-slate-800' : 'text-slate-400'; ?>">2. Verifikasi Laboran</h4>
                        <p class="text-[11px] text-slate-400">Pengecekan urgensi & ketersediaan stok lab.</p>
                    </div>

                    <!-- STEP 3: ACC Kepala Sekolah -->
                    <?php
                    $is_kepsek_pass = in_array($status, array('Disetujui Kepsek', 'Selesai'));
                    ?>
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full <?= $is_kepsek_pass ? 'bg-emerald-500 ring-emerald-50' : 'bg-slate-300 ring-slate-100'; ?> ring-4 text-white flex items-center justify-center text-[8px]">
                            <i class="fa-solid <?= $is_kepsek_pass ? 'fa-check' : 'fa-clock'; ?>"></i>
                        </div>
                        <h4 class="text-xs font-bold <?= $is_kepsek_pass ? 'text-slate-800' : 'text-slate-400'; ?>">3. Persetujuan Kepsek</h4>
                        <p class="text-[11px] text-slate-400">ACC anggaran & persetujuan Kepala Sekolah.</p>
                    </div>

                    <!-- STEP 4: Barang Diterima -->
                    <?php
                    $is_done = ($status === 'Selesai');
                    ?>
                    <div class="relative">
                        <div class="absolute -left-6 top-0.5 w-4 h-4 rounded-full <?= $is_done ? 'bg-emerald-500 ring-emerald-50' : 'bg-slate-300 ring-slate-100'; ?> ring-4 text-white flex items-center justify-center text-[8px]">
                            <i class="fa-solid <?= $is_done ? 'fa-check' : 'fa-box'; ?>"></i>
                        </div>
                        <h4 class="text-xs font-bold <?= $is_done ? 'text-slate-800' : 'text-slate-400'; ?>">4. Barang Diterima</h4>
                        <p class="text-[11px] text-slate-400">Barang telah dibeli dan masuk ke laboratorium.</p>
                    </div>

                </div>
            </div>

            <div class="pt-6 border-t border-slate-100 text-center">
                <span class="text-[11px] text-slate-400">Sistem Informasi Lab Keperawatan v1.0</span>
            </div>
        </div>

    </div>
</div>