<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Edit Data Pengguna</h2>
            <p class="text-xs text-slate-500">Perbarui rincian informasi pengguna.</p>
        </div>
        <a href="<?= site_url('users'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">
        <form action="<?= site_url('users/proses_edit'); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <input type="hidden" name="id_user" value="<?= $user->id_user; ?>">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">NIK/NISN <span class="text-rose-500">*</span></label>
                    <input type="text" name="nip_nik_user" value="<?= html_escape($user->nip_nik_user); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA LENGKAP <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_lengkap" value="<?= html_escape($user->nama_lengkap); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">USERNAME <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" value="<?= html_escape($user->username); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">PASSWORD BARU <span class="text-slate-400 font-normal">(Kosongkan jika tidak diganti)</span></label>
                    <input type="password" name="password" placeholder="••••••••" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">EMAIL <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" value="<?= html_escape($user->email); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">ROLE <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800">
                        <option value="siswa" <?= ($user->role === 'siswa') ? 'selected' : ''; ?>>Siswa</option>
                        <option value="guru" <?= ($user->role === 'guru') ? 'selected' : ''; ?>>Guru</option>
                        <option value="laboran" <?= ($user->role === 'laboran') ? 'selected' : ''; ?>>Laboran</option>
                        <option value="kepsek" <?= ($user->role === 'kepsek') ? 'selected' : ''; ?>>Kepala Sekolah</option>
                        <option value="admin" <?= ($user->role === 'admin') ? 'selected' : ''; ?>>Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">STATUS AKUN</label>
                    <select name="status_aktif" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800">
                        <option value="1" <?= ($user->status_aktif == 1) ? 'selected' : ''; ?>>Aktif</option>
                        <option value="0" <?= ($user->status_aktif == 0) ? 'selected' : ''; ?>>Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">GANTI FOTO PROFIL</label>
                <?php if (!empty($user->foto) && file_exists('./uploads/users/' . $user->foto)): ?>
                    <div class="mb-2 flex items-center gap-3">
                        <img src="<?= base_url('uploads/users/' . $user->foto); ?>" class="w-12 h-12 rounded-full object-cover border border-slate-200">
                        <span class="text-xs text-slate-400">Foto profil saat ini</span>
                    </div>
                <?php endif; ?>
                <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('users'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fa-solid fa-floppy-disk"></i> Perbarui User
                </button>
            </div>
        </form>
    </div>
</div>