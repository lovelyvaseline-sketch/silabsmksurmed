<div class="p-6">
    <?php
    $role_user     = strtolower($this->session->userdata('role') ?? '');
    $id_user_login = $this->session->userdata('id_user');
    ?>

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">
                <?= ($role_user === 'kepsek') ? 'Monitoring Peminjaman Alat' : 'Peminjaman Alat'; ?>
            </h2>
            <p class="text-xs text-slate-500">Pantau dan kelola pengajuan serta status peminjaman instrumen praktikum.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <?php if ($role_user !== 'kepsek'): ?>
                <a href="<?= site_url('peminjaman/tambah'); ?>" class="bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-plus"></i> Pinjam Alat Baru
                </a>
            <?php endif; ?>
            <!-- TOMBOL EXPORT EXCEL (ADMIN, KEPSEK, LABORAN) -->
            <?php if (in_array($role_user, array('admin', 'kepsek', 'laboran'))): ?>
                <a href="<?= site_url('peminjaman/export_laporan'); ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
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
            <form action="<?= site_url('peminjaman'); ?>" method="GET" class="w-full sm:w-80 flex items-center gap-2">
                <?php if (!empty($per_page)): ?>
                    <input type="hidden" name="limit" value="<?= $per_page; ?>">
                <?php endif; ?>

                <div class="relative w-full">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                        <i class="fa-solid fa-magnifying-glass text-xs"></i>
                    </span>
                    <input type="text" name="q" value="<?= html_escape($keyword ?? ''); ?>"
                        placeholder="Cari kode, peminjam, nama alat..."
                        class="w-full pl-9 pr-8 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent transition text-slate-700">

                    <?php if (!empty($keyword)): ?>
                        <a href="<?= site_url('peminjaman'); ?>" class="absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400 hover:text-slate-600" title="Reset">
                            <i class="fa-solid fa-xmark text-xs"></i>
                        </a>
                    <?php endif; ?>
                </div>

                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-3 py-2 rounded-xl transition shadow-sm">
                    Cari
                </button>
            </form>

            <?php if (!empty($keyword)): ?>
                <div class="text-xs text-slate-500">
                    Hasil pencarian: <strong class="text-slate-800">"<?= html_escape($keyword); ?>"</strong>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tabel Data -->
        <div class="max-h-[600px] overflow-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead class="sticky top-0 z-10 bg-slate-50">
                    <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50/70">
                        <th class="py-3.5 px-4 text-center w-12">NO</th>
                        <th class="py-3.5 px-4">KODE PINJAM</th>
                        <th class="py-3.5 px-4">PEMINJAM</th>
                        <th class="py-3.5 px-4">NAMA BARANG</th>
                        <th class="py-3.5 px-4 text-center">TANGGAL PINJAM</th>
                        <th class="py-3.5 px-4 text-center">RENCANA KEMBALI</th>
                        <th class="py-3.5 px-4 text-center">STATUS</th>
                        <th class="py-3.5 px-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    <?php if (!empty($peminjaman)): ?>
                        <?php
                        $no = isset($offset) ? ($offset + 1) : 1;
                        foreach ($peminjaman as $p):
                            // Pembacaan Fleksibel (Aman untuk Object maupun Array)
                            $id_peminjaman       = is_object($p) ? $p->id_peminjaman : $p['id_peminjaman'];
                            $kode_peminjaman     = is_object($p) ? $p->kode_peminjaman : $p['kode_peminjaman'];
                            $id_user_item        = is_object($p) ? $p->id_user : $p['id_user'];
                            $peminjam            = is_object($p) ? ($p->peminjam ?? ($p->nama_lengkap ?? '-')) : ($p['peminjam'] ?? ($p['nama_lengkap'] ?? '-'));
                            $nama_barang         = is_object($p) ? $p->nama_barang : $p['nama_barang'];
                            $tgl_pinjam          = is_object($p) ? $p->tgl_pinjam : $p['tgl_pinjam'];
                            $tgl_kembali_rencana = is_object($p) ? $p->tgl_kembali_rencana : $p['tgl_kembali_rencana'];
                            $status              = is_object($p) ? $p->status : $p['status'];

                            $badge_class = 'bg-amber-50 text-amber-600 border-amber-200';
                            if ($status == 'Dipinjam') $badge_class = 'bg-sky-50 text-sky-600 border-sky-200';
                            elseif ($status == 'Dikembalikan') $badge_class = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                            elseif ($status == 'Ditolak') $badge_class = 'bg-rose-50 text-rose-600 border-rose-200';
                        ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 text-center font-medium text-slate-400"><?= $no++; ?></td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800"><?= html_escape($kode_peminjaman); ?></td>
                                <td class="py-3.5 px-4 font-medium text-slate-800"><?= html_escape($peminjam); ?></td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium"><?= html_escape($nama_barang); ?></td>
                                <td class="py-3.5 px-4 text-center text-slate-600"><?= date('d/m/Y', strtotime($tgl_pinjam)); ?></td>
                                <td class="py-3.5 px-4 text-center text-slate-600"><?= date('d/m/Y', strtotime($tgl_kembali_rencana)); ?></td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold border <?= $badge_class; ?>">
                                        <?= html_escape($status); ?>
                                    </span>
                                </td>

                                <!-- Kolom Aksi -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">

                                        <!-- 1. Detail (Semua User) -->
                                        <a href="<?= site_url('peminjaman/detail/' . $id_peminjaman); ?>" class="text-teal-600 hover:text-teal-700 p-1.5 transition rounded-lg hover:bg-slate-100" title="Detail Peminjaman">
                                            <i class="fa-solid fa-eye text-base"></i>
                                        </a>


                                        <!-- 2. Tombol Approval Khusus Laboran & Admin -->
                                        <?php if (in_array($role_user, ['admin', 'laboran']) && $status === 'Pending'): ?>
                                            <form action="<?= site_url('peminjaman/update_status/' . $id_peminjaman); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Setujui peminjaman alat ini? Stok akan otomatis berkurang.')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="status_tujuan" value="Dipinjam">
                                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs px-2 py-1 rounded-lg font-semibold transition">
                                                    Setujui
                                                </button>
                                            </form>
                                            <form action="<?= site_url('peminjaman/update_status/' . $id_peminjaman); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Tolak peminjaman ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <input type="hidden" name="status_tujuan" value="Ditolak">
                                                <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white text-xs px-2 py-1 rounded-lg font-semibold transition">
                                                    Tolak
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- 3. Tombol Pengembalian Khusus Laboran & Admin -->
                                        <?php if (in_array($role_user, ['admin', 'laboran']) && $status === 'Dipinjam'): ?>
                                            <a href="<?= site_url('peminjaman/konfirmasi_kembali/' . $id_peminjaman); ?>" class="bg-sky-600 hover:bg-sky-700 text-white text-xs px-2 py-1 rounded-lg font-semibold transition">
                                                Kembalikan
                                            </a>
                                        <?php endif; ?>

                                        <!-- 4. Tombol Edit (Admin, Laboran, atau Guru/Siswa saat status Pending) -->
                                        <?php if (in_array($role_user, ['admin']) || ($status === 'Pending' && $id_user_item == $id_user_login)): ?>
                                            <a href="<?= site_url('peminjaman/edit/' . $id_peminjaman); ?>" class="text-amber-500 hover:text-amber-600 p-1.5 transition rounded-lg hover:bg-amber-50" title="Edit Peminjaman">
                                                <i class="fa-solid fa-pen-to-square text-base"></i>
                                            </a>
                                        <?php endif; ?>
                                        <!-- 3. TOMBOL BATALKAN (Khusus Guru & Siswa saat Status Masih Pending) -->
                                        <?php if (in_array($role_user, array('guru', 'siswa')) && $status === 'Pending' && $id_user_item == $id_user_login): ?>
                                            <form action="<?= site_url('peminjaman/batalkan/' . $id_peminjaman); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pengajuan peminjaman ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <button type="submit" class="bg-rose-50 text-rose-600 hover:bg-rose-100 border border-rose-200 text-xs px-2.5 py-1 rounded-lg font-semibold transition flex items-center gap-1" title="Batalkan Pengajuan">
                                                    <i class="fa-solid fa-xmark"></i> Batalkan
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- 5. Tombol Hapus: HANYA DITAMPILKAN UNTUK ADMIN -->
                                        <?php if ($role_user === 'admin'): ?>
                                            <form action="<?= site_url('peminjaman/delete/' . $id_peminjaman); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen data peminjaman ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <button type="submit" class="text-rose-500 hover:text-rose-600 p-1.5 transition rounded-lg hover:bg-rose-50" title="Hapus Data (Khusus Admin)">
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
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                <?= !empty($keyword) ? 'Tidak ditemukan transaksi yang cocok dengan kata kunci "' . html_escape($keyword) . '"' : 'Belum ada data peminjaman.'; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer Tabel: Pilihan Limit & Pagination -->
        <?php if (isset($total_rows)): ?>
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4 px-6 py-4 border-t border-slate-100 bg-white">
                <div class="flex items-center gap-3 text-xs text-slate-500">
                    <span class="flex items-center gap-1.5">
                        Tampilkan
                        <select onchange="changeLimit(this.value)" class="py-1 px-2 text-xs border border-slate-200 rounded-lg bg-white focus:outline-none focus:ring-1 focus:ring-teal-500 font-medium text-slate-700 cursor-pointer">
                            <option value="5" <?= (isset($per_page) && $per_page == 5) ? 'selected' : ''; ?>>5</option>
                            <option value="10" <?= (isset($per_page) && $per_page == 10) ? 'selected' : ''; ?>>10</option>
                            <option value="25" <?= (isset($per_page) && $per_page == 25) ? 'selected' : ''; ?>>25</option>
                            <option value="50" <?= (isset($per_page) && $per_page == 50) ? 'selected' : ''; ?>>50</option>
                        </select>
                        data
                    </span>
                    <span class="text-slate-300">|</span>
                    <span>
                        Menampilkan <strong class="text-slate-700"><?= ($total_rows > 0) ? ($offset + 1) : 0; ?></strong> - <strong class="text-slate-700"><?= min($offset + $per_page, $total_rows); ?></strong> dari <strong class="text-slate-700"><?= $total_rows; ?></strong> data
                    </span>
                </div>

                <div>
                    <?= $pagination ?? ''; ?>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<script>
    function changeLimit(limit) {
        const url = new URL(window.location.href);
        url.searchParams.set('limit', limit);
        url.searchParams.set('page', 0);
        window.location.href = url.toString();
    }
</script>