<div class="p-6">
    <?php
    $role_user     = strtolower($this->session->userdata('role') ?? '');
    $id_user_login = $this->session->userdata('id_user');
    ?>

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                <?= ($role_user === 'kepsek') ? 'ACC & Monitoring Pengadaan Alat/BHP' : 'Pengadaan Alat & Bahan (BHP)'; ?>
            </h2>
            <p class="text-xs text-slate-500">Kelola dan pantau pengajuan pengadaan instrumen serta bahan habis pakai laboratorium keperawatan.</p>
        </div>

        <div  class="flex flex-wrap items-center gap-2 sm:justify-end">
            <!-- IZINKAN GURU, LABORAN, DAN ADMIN UNTUK MENGAJUKAN PENGADAAN BARU -->
            <?php if (in_array($role_user, array('guru', 'laboran', 'admin'))): ?>
                <a href="<?= site_url('pengadaan/tambah'); ?>" class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Ajukan Pengadaan Baru
                </a>
            <?php endif; ?>
        <!-- TOMBOL EXPORT EXCEL (ADMIN, KEPSEK, LABORAN) -->
        <?php if (in_array($role_user, array('admin', 'kepsek', 'laboran'))): ?>
            <a href="<?= site_url('pengadaan/proses_export_excel'); ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-excel text-sm"></i> Export Excel
            </a>
        <?php endif; ?>
        </div>
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

    <!-- Card Tabel & Form Search -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        <!-- Form Pencarian -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <form action="<?= site_url('pengadaan'); ?>" method="GET" class="w-full sm:w-80 flex items-center gap-2">
                <input type="text" name="q" value="<?= html_escape($keyword ?? ''); ?>"
                    placeholder="Cari item, pengaju, alasan..."
                    class="w-full pl-3 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 transition text-slate-700">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-3 py-2 rounded-xl transition shadow-sm">
                    Cari
                </button>

            </form>
        </div>

        <!-- Tabel Data Pengadaan -->
        <div class="max-h-[600px] overflow-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead class="sticky top-0 z-10 bg-slate-50">
                    <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50/70">
                        <th class="py-3.5 px-4 text-center w-12">NO</th>
                        <th class="py-3.5 px-4">NAMA ITEM / BARANG</th>
                        <th class="py-3.5 px-4 text-center">JUMLAH</th>
                        <th class="py-3.5 px-4">ESTIMASI HARGA</th>
                        <th class="py-3.5 px-4">PENGAJU</th>
                        <th class="py-3.5 px-4 text-center">STATUS APPROVAL</th>
                        <th class="py-3.5 px-4 text-center">AKSI & APPROVAL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    <?php if (!empty($pengadaan)): ?>
                        <?php
                        $no = 1;
                        foreach ($pengadaan as $p):
                            $id_pengadaan   = is_object($p) ? $p->id_pengadaan : $p['id_pengadaan'];
                            $nama_item      = is_object($p) ? $p->nama_item : $p['nama_item'];
                            $jumlah         = is_object($p) ? $p->jumlah : $p['jumlah'];
                            $satuan         = is_object($p) ? ($p->satuan ?? 'Buah') : ($p['satuan'] ?? 'Buah');
                            $estimasi_harga = is_object($p) ? ($p->estimasi_harga ?? 0) : ($p['estimasi_harga'] ?? 0);
                            $id_pengaju     = is_object($p) ? $p->id_pengaju : $p['id_pengaju'];
                            $nama_pengaju   = is_object($p) ? ($p->nama_pengaju ?? ($p->nama_lengkap ?? '-')) : ($p['nama_pengaju'] ?? ($p['nama_lengkap'] ?? '-'));
                            $status         = is_object($p) ? ($p->status_approval ?? 'Pending') : ($p['status_approval'] ?? 'Pending');

                            // Visual Badge Berdasarkan Status Approval Bertingkat
                            $badge_class = 'bg-amber-50 text-amber-600 border-amber-200';
                            if ($status === 'Disetujui Laboran') {
                                $badge_class = 'bg-sky-50 text-sky-600 border-sky-200';
                            } elseif ($status === 'Disetujui Kepsek') {
                                $badge_class = 'bg-indigo-50 text-indigo-600 border-indigo-200';
                            } elseif ($status === 'Selesai') {
                                $badge_class = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                            } elseif ($status === 'Ditolak') {
                                $badge_class = 'bg-rose-50 text-rose-600 border-rose-200';
                            }
                        ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 text-center font-medium text-slate-400"><?= $no++; ?></td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800"><?= html_escape($nama_item); ?></td>
                                <td class="py-3.5 px-4 text-center font-bold text-teal-600"><?= $jumlah . ' ' . html_escape($satuan); ?></td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">Rp <?= number_format($estimasi_harga, 0, ',', '.'); ?></td>
                                <td class="py-3.5 px-4 font-medium text-slate-700"><?= html_escape($nama_pengaju); ?></td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold border <?= $badge_class; ?>">
                                        <?= html_escape($status); ?>
                                    </span>
                                </td>

                                <!-- Kolom Aksi & Approval Bertingkat -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <!-- 1. Detail Pengadaan -->
                                        <a href="<?= site_url('pengadaan/detail/' . $id_pengadaan); ?>" class="text-teal-600 hover:text-teal-700 p-1.5 transition rounded-lg hover:bg-slate-100" title="Detail Pengadaan">
                                            <i class="fa-solid fa-eye text-base"></i>
                                        </a>

                                        <!-- 2. APPROVAL TAHAP 1: KHUSUS LABORAN (Saat Status Pending) -->
                                        <?php if (in_array($role_user, array('laboran', 'admin')) && $status === 'Pending'): ?>
                                            <form action="<?= site_url('pengadaan/update_status/' . $id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Verifikasi pengajuan ini dan teruskan ke Kepala Sekolah?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="status_baru" value="Disetujui Laboran">
                                                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white text-xs px-2.5 py-1 rounded-lg font-semibold transition flex items-center gap-1">
                                                    <i class="fa-solid fa-check"></i> Verifikasi Laboran
                                                </button>
                                            </form>
                                            <form action="<?= site_url('pengadaan/update_status/' . $id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Tolak pengajuan ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="status_baru" value="Ditolak Laboran">
                                                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-xs px-2.5 py-1 rounded-lg font-semibold transition">
                                                    Tolak
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- 3. APPROVAL TAHAP 2: KHUSUS KEPALA SEKOLAH (Saat Status Disetujui Laboran) -->
                                        <?php if (in_array($role_user, array('kepsek', 'admin')) && $status === 'Disetujui Laboran'): ?>
                                            <form action="<?= site_url('pengadaan/update_status/' . $id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('ACC dan setujui pengadaan barang ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="status_baru" value="Disetujui Kepsek">
                                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs px-2.5 py-1 rounded-lg font-semibold transition flex items-center gap-1">
                                                    <i class="fa-solid fa-signature"></i> ACC Kepsek
                                                </button>
                                            </form>
                                            <form action="<?= site_url('pengadaan/update_status/' . $id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Tolak pengajuan pengadaan ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="status_baru" value="Ditolak Kepsek">
                                                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-xs px-2.5 py-1 rounded-lg font-semibold transition">
                                                    Tolak
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- 4. TAHAP AKHIR: BARANG DITERIMA (Saat Status Disetujui Kepsek) -->
                                        <?php if (in_array($role_user, array('admin', 'laboran')) && $status === 'Disetujui Kepsek'): ?>
                                            <form action="<?= site_url('pengadaan/update_status/' . $id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Tandai barang sudah diterima dan masuk laboratorium?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="status_baru" value="Selesai">
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-2.5 py-1 rounded-lg font-semibold transition flex items-center gap-1">
                                                    <i class="fa-solid fa-box-open"></i> Barang Diterima
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- 5. Tombol Edit (Pengaju / Admin jika masih Pending) -->
                                        <?php if (($status === 'Pending' && $id_pengaju == $id_user_login) || $role_user === 'admin'): ?>
                                            <a href="<?= site_url('pengadaan/edit/' . $id_pengadaan); ?>" class="text-amber-500 hover:text-amber-600 p-1.5 transition rounded-lg hover:bg-amber-50" title="Edit Pengadaan">
                                                <i class="fa-solid fa-pen-to-square text-base"></i>
                                            </a>
                                        <?php endif; ?>

                                        <!-- 6. TOMBOL HAPUS DATA (KHUSUS ADMIN & KEPALA SEKOLAH) -->
                                        <?php if (in_array($role_user, array('admin', 'kepsek'))): ?>
                                            <form action="<?= site_url('pengadaan/delete/' . $id_pengadaan); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data pengajuan pengadaan ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <button type="submit" class="text-rose-500 hover:text-rose-600 p-1.5 transition rounded-lg hover:bg-rose-50" title="Hapus Data (Khusus Admin & Kepsek)">
                                                    <i class="fa-solid fa-trash-can text-base"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data pengajuan pengadaan.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
</div>