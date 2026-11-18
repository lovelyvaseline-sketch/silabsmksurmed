<div class="p-6">
    <?php
    // Deklarasi variabel $role_user dari session login
    $role_user     = strtolower($this->session->userdata('role') ?? '');
    $id_user_login = $this->session->userdata('id_user');
    ?>
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Laporan Kerusakan Alat</h2>
            <p class="text-xs text-slate-500">Monitoring instrumen praktikum yang mengalami kerusakan beserta riwayat perbaikannya.</p>
        </div>
        <div>
            <!-- TOMBOL EXPORT EXCEL (ADMIN, KEPSEK, LABORAN) -->
            <?php if (in_array($role_user, array('admin', 'kepsek', 'laboran'))): ?>
                <a href="<?= site_url('kerusakan_alat/export_laporan'); ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
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

    <!-- Card Tabel Data -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">

        <!-- Form Pencarian -->
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <form action="<?= site_url('kerusakan_alat'); ?>" method="GET" class="w-full sm:w-80 flex items-center gap-2">
                <input type="text" name="q" value="<?= html_escape($keyword ?? ''); ?>" placeholder="Cari kode pinjam, barang, peminjam..." class="w-full pl-3 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-3 py-2 rounded-xl transition shadow-sm">Cari</button>
            </form>
        </div>

        <div class="max-h-[600px] overflow-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead class="sticky top-0 z-10 bg-slate-50">
                    <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50/70">
                        <th class="py-3.5 px-4 text-center w-12">NO</th>
                        <th class="py-3.5 px-4">KODE PINJAM</th>
                        <th class="py-3.5 px-4">NAMA ALAT</th>
                        <th class="py-3.5 px-4">PEMINJAM</th>
                        <th class="py-3.5 px-4 text-center">KONDISI</th>
                        <th class="py-3.5 px-4 text-center">STATUS PERBAIKAN</th>
                        <th class="py-3.5 px-4">DESKRIPSI KERUSAKAN</th>
                        <th class="py-3.5 px-4 text-center">UPDATE STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    <?php if (!empty($kerusakan)): ?>
                        <?php $no = 1;
                        foreach ($kerusakan as $k):
                            // Pembacaan Variabel Aman dengan Null Coalescing Operator
                            $id_kerusakan      = is_object($k) ? $k->id_kerusakan : $k['id_kerusakan'];
                            $kode_peminjaman   = is_object($k) ? ($k->kode_peminjaman ?? '-') : ($k['kode_peminjaman'] ?? '-');
                            $nama_barang       = is_object($k) ? $k->nama_barang : $k['nama_barang'];
                            $nama_peminjam     = is_object($k) ? ($k->nama_peminjam ?? '-') : ($k['nama_peminjam'] ?? '-');
                            $kondisi_kerusakan = is_object($k) ? ($k->kondisi_kerusakan ?? 'Rusak Ringan') : ($k['kondisi_kerusakan'] ?? 'Rusak Ringan');

                            // Menangani variabel status_perbaikan jika belum terdefinisi di database
                            $status_perbaikan  = is_object($k) ? ($k->status_perbaikan ?? 'Menunggu Perbaikan') : ($k['status_perbaikan'] ?? 'Menunggu Perbaikan');
                            $deskripsi         = is_object($k) ? ($k->deskripsi_kerusakan ?? '-') : ($k['deskripsi_kerusakan'] ?? '-');

                            $badge_status = 'bg-amber-50 text-amber-600 border-amber-200';
                            if ($status_perbaikan === 'Sedang Diperbaiki') $badge_status = 'bg-sky-50 text-sky-600 border-sky-200';
                            elseif ($status_perbaikan === 'Selesai Diperbaiki') $badge_status = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                            elseif ($status_perbaikan === 'Afkir/Diganti') $badge_status = 'bg-rose-50 text-rose-600 border-rose-200';
                        ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 text-center font-medium text-slate-400"><?= $no++; ?></td>
                                <td class="py-3.5 px-4 font-semibold text-slate-800"><?= html_escape($kode_peminjaman); ?></td>
                                <td class="py-3.5 px-4 font-medium text-teal-600"><?= html_escape($nama_barang); ?></td>
                                <td class="py-3.5 px-4 text-slate-700 font-medium"><?= html_escape($nama_peminjam); ?></td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-1 text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 rounded-full">
                                        <?= html_escape($kondisi_kerusakan); ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold border <?= $badge_status; ?>">
                                        <?= html_escape($status_perbaikan); ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-slate-600 text-xs italic">
                                    <?= html_escape($deskripsi); ?>
                                </td>

                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <form action="<?= site_url('kerusakan_alat/update_status'); ?>" method="POST" class="flex items-center justify-center gap-1.5">
                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                        <input type="hidden" name="id_kerusakan" value="<?= $id_kerusakan; ?>">
                                        <select
                                            name="status_perbaikan"
                                            onchange="this.form.submit()"
                                            <?= !in_array(
                                                $status_perbaikan,
                                                ['Menunggu Perbaikan', 'Sedang Diperbaiki'],
                                                TRUE
                                            ) ? 'disabled' : ''; ?>
                                            class="py-1 px-2 text-xs border border-slate-200 rounded-lg bg-white font-medium text-slate-700 focus:outline-none focus:ring-1 focus:ring-teal-500 cursor-pointer"
                                        >
                                            <option value="Menunggu Perbaikan" <?= ($status_perbaikan === 'Menunggu Perbaikan') ? 'selected' : ''; ?>>Menunggu Perbaikan</option>
                                            <option value="Sedang Diperbaiki" <?= ($status_perbaikan === 'Sedang Diperbaiki') ? 'selected' : ''; ?>>Sedang Diperbaiki</option>
                                            <option value="Selesai Diperbaiki" <?= ($status_perbaikan === 'Selesai Diperbaiki') ? 'selected' : ''; ?>>Selesai Diperbaiki</option>
                                            <option value="Afkir/Diganti" <?= ($status_perbaikan === 'Afkir/Diganti') ? 'selected' : ''; ?>>Afkir / Diganti</option>
                                        </select>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Belum ada laporan kerusakan alat.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>