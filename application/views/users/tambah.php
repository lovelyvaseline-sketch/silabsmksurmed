<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Tambah Pengguna Baru</h2>
            <p class="text-xs text-slate-500">Isi formulir berikut untuk mendaftarkan akun pengguna baru ke sistem.</p>
        </div>
        <a href="<?= site_url('users'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-3xl">
        <form action="<?= site_url('users/simpan'); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
            <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">NIP / NIK USER <span class="text-rose-500">*</span></label>
                    <input type="text" name="nip_nik_user" required placeholder="Contoh: 19880526..." class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">NAMA LENGKAP <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_lengkap" required placeholder="Nama beserta gelar" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">USERNAME <span class="text-rose-500">*</span></label>
                    <input type="text" name="username" required placeholder="Username login" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">PASSWORD <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">EMAIL <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="user@gmail.com" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">ROLE <span class="text-rose-500">*</span></label>
                    <select name="role" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800">
                        <option value="siswa">Siswa</option>
                        <option value="guru">Guru</option>
                        <option value="laboran">Laboran</option>
                        <option value="kepsek">Kepala Sekolah</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">STATUS AKUNT</label>
                    <select name="status_aktif" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">FOTO PROFIL (OPSIONAL)</label>
                <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <a href="<?= site_url('users'); ?>" class="px-5 py-2.5 text-xs font-semibold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">Batal</a>
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan User
                </button>
            </div>
        </form>
    </div>
</div>