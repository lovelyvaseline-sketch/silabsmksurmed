<div class="p-6">
    <?php
    $role_user = strtolower($this->session->userdata('role') ?? '');

    // Pembacaan aman ID Peminjaman
    $id_peminjaman = is_object($peminjaman)
        ? ($peminjaman->id_peminjaman ?? ($peminjaman->id ?? ''))
        : ($peminjaman['id_peminjaman'] ?? ($peminjaman['id'] ?? ''));

    // Pembacaan aman Kode Peminjaman
    $kode_peminjaman = is_object($peminjaman)
        ? ($peminjaman->kode_peminjaman ?? '-')
        : ($peminjaman['kode_peminjaman'] ?? '-');


    // Pembacaan nama peminjam dengan fallback yang fleksibel
    $peminjam = is_object($peminjaman)
        ? ($peminjaman->peminjam ?? ($peminjaman->nama_lengkap ?? '-'))
        : ($peminjaman['peminjam'] ?? ($peminjaman['nama_lengkap'] ?? '-'));

    // Pembacaan aman Nama Barang
    $nama_barang = is_object($peminjaman)
        ? ($peminjaman->nama_barang ?? '-')
        : ($peminjaman['nama_barang'] ?? '-');

    // Pembacaan aman Jumlah & Satuan (Mencegah Undefined Index)
    $jumlah = is_object($peminjaman)
        ? ($peminjaman->jumlah ?? ($peminjaman->qty ?? ($peminjaman->jumlah_pinjam ?? 1)))
        : ($peminjaman['jumlah'] ?? ($peminjaman['qty'] ?? ($peminjaman['jumlah_pinjam'] ?? 1)));

    $satuan = is_object($peminjaman)
        ? ($peminjaman->satuan ?? 'Set')
        : ($peminjaman['satuan'] ?? 'Set');

    // Pembacaan aman Tanggal
    $tgl_pinjam = is_object($peminjaman)
        ? ($peminjaman->tgl_pinjam ?? date('Y-m-d'))
        : ($peminjaman['tgl_pinjam'] ?? date('Y-m-d'));

    $tgl_kembali_rencana = is_object($peminjaman)
        ? ($peminjaman->tgl_kembali_rencana ?? date('Y-m-d'))
        : ($peminjaman['tgl_kembali_rencana'] ?? date('Y-m-d'));

    // Pembacaan aman Status
    $status = is_object($peminjaman)
        ? ($peminjaman->status ?? 'Pending')
        : ($peminjaman['status'] ?? 'Pending');

    // Pembacaan aman Catatan
    $catatan = is_object($peminjaman)
        ? ($peminjaman->catatan ?? '')
        : ($peminjaman['catatan'] ?? '');
    $catatan = trim((string) $catatan) !== '' ? $catatan : '-';
    ?>

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Detail Peminjaman Alat</h2>
            <p class="text-xs text-slate-500">Rincian informasi transaksi peminjaman instrumen laboratorium.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="<?= site_url('peminjaman'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>

            <!-- Tombol Edit Data: Hanya muncul jika Role Admin/Laboran DAN status masih Pending -->
            <?php if (in_array($role_user, ['admin', 'laboran']) && $status === 'Pending'): ?>
                <a href="<?= site_url('peminjaman/edit/' . $id_peminjaman); ?>" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fa-solid fa-pen-to-square"></i> Edit Data
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Content Detail Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm text-slate-700">
            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Kode Peminjaman</label>
                <p class="font-semibold text-slate-800 text-base mt-1">
                    <?= html_escape($kode_peminjaman); ?>
                </p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">PEMINJAM</label>
                <p class="font-medium text-slate-800 mt-1">
                    <?= html_escape($peminjam); ?>
                </p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Nama Alat Praktikum</label>
                <p class="font-medium text-slate-800 mt-1">
                    <?= html_escape($nama_barang); ?>
                </p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Jumlah Pinjam</label>
                <p class="font-bold text-teal-600 mt-1">
                    <?= html_escape($jumlah . ' ' . $satuan); ?>
                </p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Tanggal Pinjam</label>
                <p class="font-medium text-slate-700 mt-1">
                    <?= date('d/m/Y', strtotime($tgl_pinjam)); ?>
                </p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Rencana Kembali</label>
                <p class="font-medium text-slate-700 mt-1">
                    <?= date('d/m/Y', strtotime($tgl_kembali_rencana)); ?>
                </p>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Status Peminjaman</label>
                <div class="mt-1">
                    <?php
                    $badge_class = 'bg-slate-100 text-slate-600 border-slate-200';
                    if ($status == 'Pending') $badge_class = 'bg-amber-50 text-amber-600 border-amber-200';
                    elseif ($status == 'Dipinjam') $badge_class = 'bg-sky-50 text-sky-600 border-sky-200';
                    elseif ($status == 'Dikembalikan') $badge_class = 'bg-emerald-50 text-emerald-600 border-emerald-200';
                    elseif ($status == 'Ditolak') $badge_class = 'bg-rose-50 text-rose-600 border-rose-200';
                    ?>
                    <span class="inline-block px-3 py-1 text-xs rounded-full font-semibold border <?= $badge_class; ?>">
                        <?= html_escape($status); ?>
                    </span>
                </div>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-400 uppercase">Keperluan / Keterangan</label>
                <p class="font-medium text-slate-700 mt-1">
                    <?= html_escape($catatan); ?>
                </p>
            </div>
        </div>
    </div>
</div>