<div class="p-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Manajemen Pengguna (Users)</h2>
            <p class="text-xs text-slate-500">Kelola akun Admin, Laboran, Guru, Siswa, dan Kepala Sekolah.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="<?= site_url('users/export_laporan'); ?>" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-file-excel text-sm"></i> Export Excel
            </a>
            <a href="<?= site_url('users/tambah'); ?>" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fa-solid fa-user-plus text-sm"></i> Tambah User Baru
            </a>
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

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <!-- Form Search -->
        <div class="p-4 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <form action="<?= site_url('users'); ?>" method="GET" class="w-full sm:w-80 flex items-center gap-2">
                <input type="text" name="q" value="<?= html_escape($keyword ?? ''); ?>" placeholder="Cari nama, username, NIP/NIK..." class="w-full pl-3 pr-3 py-2 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-700">
                <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold px-3 py-2 rounded-xl transition">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse table-auto">
                <thead>
                    <tr class="border-b border-slate-100 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-50/70">
                        <th class="py-3.5 px-4 text-center w-12">NO</th>
                        <th class="py-3.5 px-4">USER</th>
                        <th class="py-3.5 px-4">NIP / NIK</th>
                        <th class="py-3.5 px-4">EMAIL</th>
                        <th class="py-3.5 px-4 text-center">ROLE</th>
                        <th class="py-3.5 px-4 text-center">STATUS</th>
                        <th class="py-3.5 px-4 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm text-slate-700">
                    <?php if (!empty($users)): $no = $start_num;
                        foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 text-center font-medium text-slate-400"><?= $no++; ?></td>
                                <td class="py-3.5 px-4">
                                    <div class="flex items-center gap-3">
                                        <?php if (!empty($u->foto) && file_exists(FCPATH . 'uploads/users/' . $u->foto)): ?>
                                            <img src="<?= base_url('uploads/users/' . $u->foto); ?>" class="w-9 h-9 rounded-full object-cover border border-slate-200">
                                        <?php else: ?>
                                            <div class="w-9 h-9 rounded-full bg-teal-100 text-teal-700 font-bold flex items-center justify-center text-xs">
                                                <?= strtoupper(substr($u->nama_lengkap, 0, 1)); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="font-bold text-slate-800 block leading-tight"><?= html_escape($u->nama_lengkap); ?></span>
                                            <span class="text-xs text-slate-400">@<?= html_escape($u->username); ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-4 font-mono text-xs font-semibold text-slate-600"><?= html_escape($u->nip_nik_user); ?></td>
                                <td class="py-3.5 px-4 font-medium text-slate-600"><?= html_escape($u->email); ?></td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="uppercase text-[10px] font-bold px-2.5 py-1 rounded-full border border-slate-200 bg-slate-50 text-slate-600">
                                        <?= html_escape($u->role); ?>
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <?php if ($u->status_aktif == 1): ?>
                                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-full">Aktif</span>
                                    <?php else: ?>
                                        <span class="text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-full">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <a href="<?= site_url('users/edit/' . $u->id_user); ?>" class="text-amber-500 hover:text-amber-600 p-1.5 transition rounded-lg hover:bg-amber-50" title="Edit User">
                                            <i class="fa-solid fa-pen-to-square text-base"></i>
                                        </a>
                                        <?php if ($u->id_user != $this->session->userdata('id_user')): ?>
                                            <form action="<?= site_url('users/delete/' . $u->id_user); ?>" method="POST" class="inline-flex" onsubmit="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
                                                <button type="submit" class="text-rose-500 hover:text-rose-600 p-1.5 transition rounded-lg hover:bg-rose-50" title="Hapus User">
                                                    <i class="fa-solid fa-trash-can text-base"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach;
                    else: ?>
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada data user.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- FOOTER PAGINATION -->
        <div class="p-4 border-t border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4 bg-slate-50/50">
            <div class="text-xs text-slate-500">
                Menampilkan <span class="font-bold text-slate-700"><?= count($users); ?></span> dari total <span class="font-bold text-slate-700"><?= $total_rows; ?></span> pengguna.
            </div>
            <div>
                <?= $pagination; ?>
            </div>
        </div>

    </div>
</div>