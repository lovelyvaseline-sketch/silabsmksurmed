<div class="max-w-4xl mx-auto space-y-6">

    <!-- Notifikasi Flashdata -->
    <?php if ($this->session->flashdata('success')): ?>
        <div class="p-4 bg-emerald-100 border border-emerald-300 text-emerald-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> <?= $this->session->flashdata('success'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->session->flashdata('error')): ?>
        <div class="p-4 bg-rose-100 border border-rose-300 text-rose-800 rounded-xl text-sm font-semibold flex items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i> <?= $this->session->flashdata('error'); ?>
        </div>
    <?php endif; ?>



    <div class="p-6">
        <!-- Header Halaman -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-800">Profil Saya</h2>
            <p class="text-xs text-slate-500">Kelola informasi data diri, username, dan foto profil akun Anda.</p>
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

            <!-- CARD KIRI: FOTO PROFIL & INFO RINGKAS -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 text-center flex flex-col items-center">
                <div class="relative mb-4">
                    <?php if (!empty($user->foto) && file_exists(FCPATH . 'uploads/users/' . $user->foto)): ?>
                        <img src="<?= base_url('uploads/users/' . $user->foto); ?>"
                            alt="Foto Profil"
                            class="w-28 h-28 rounded-full object-cover border-4 border-teal-500 shadow-md">
                    <?php else: ?>
                        <div class="w-28 h-28 rounded-full bg-teal-600 text-white font-bold flex items-center justify-center text-3xl border-4 border-teal-400 shadow-md">
                            <?= strtoupper(substr($user->nama_lengkap ?? 'U', 0, 1)); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <h3 class="text-lg font-bold text-slate-800 mb-1"><?= html_escape($user->nama_lengkap); ?></h3>
                <span class="inline-block px-3 py-1 text-xs font-bold uppercase tracking-wider text-teal-700 bg-teal-50 border border-teal-200 rounded-full mb-4">
                    <?= html_escape($user->role); ?>
                </span>

                <div class="w-full pt-4 border-t border-slate-100 text-left space-y-3 text-xs">
                    <div>
                        <span class="text-slate-400 block font-semibold">NIP / NIK</span>
                        <span class="font-bold text-slate-700 font-mono"><?= html_escape($user->nip_nik_user ?? '-'); ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">USERNAME</span>
                        <span class="font-bold text-slate-700">@<?= html_escape($user->username); ?></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">STATUS AKUN</span>
                        <span class="font-bold <?= ($user->status_aktif == 1) ? 'text-emerald-600' : 'text-rose-600'; ?>">
                            <?= ($user->status_aktif == 1) ? 'Aktif' : 'Nonaktif'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- CARD KANAN: FORM EDIT PROFIL, FOTO & PASSWORD -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
                <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 mb-5">Perbarui Informasi Akun</h3>

                <form action="<?= site_url('profile/update_profile'); ?>" method="POST" enctype="multipart/form-data" class="space-y-5">
                    <input type="hidden" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">NAMA LENGKAP <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_lengkap" value="<?= html_escape($user->nama_lengkap); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-bold text-slate-800">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-2">EMAIL <span class="text-rose-500">*</span></label>
                            <input type="email" name="email" value="<?= html_escape($user->email); ?>" required class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">PASSWORD BARU <span class="text-slate-400 font-normal">(Biarkan kosong jika tidak ingin mengganti password)</span></label>
                        <input type="password" name="password" placeholder="••••••••" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">UNGGAH FOTO PROFIL BARU</label>
                        <input type="file" name="foto" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-teal-50 file:text-teal-700 hover:file:bg-teal-100">
                        <p class="text-[11px] text-slate-400 mt-1">Format file: JPG, JPEG, PNG, WEBP (Maksimal 2MB).</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-teal-600 hover:bg-teal-700 rounded-xl shadow-sm flex items-center gap-2 transition">
                            <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Profil
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>