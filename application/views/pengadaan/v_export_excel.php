<div class="p-6">
    <!-- Header Form -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Export Laporan Pengadaan Alat & BHP</h2>
            <p class="text-xs text-slate-500">Unduh berkas laporan pengajuan pengadaan laboratorium format Microsoft Excel.</p>
        </div>
        <a href="<?= site_url('pengadaan'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Card Form Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-2xl">
        <form action="<?= site_url('pengadaan/proses_export_excel'); ?>" method="GET" target="_blank" class="space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL MULAI</label>
                    <input type="date" name="tgl_mulai" value="<?= date('Y-m-01'); ?>" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL SELESAI</label>
                    <input type="date" name="tgl_selesai" value="<?= date('Y-m-d'); ?>" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <!-- Filter Status -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">FILTER STATUS APPROVAL</label>
                <select name="status" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800 cursor-pointer">
                    <option value="Semua">-- Semua Status Approval --</option>
                    <option value="Pending">Pending</option>
                    <option value="Disetujui Laboran">Disetujui Laboran</option>
                    <option value="Disetujui Kepsek">Disetujui Kepsek</option>
                    <option value="Selesai">Selesai / Diterima</option>
                    <option value="Ditolak">Ditolak</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                <button type="submit" class="px-5 py-2.5 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-sm flex items-center gap-2 transition">
                    <i class="fa-solid fa-file-excel text-base"></i> Export File Excel
                </button>
            </div>
        </form>
    </div>
</div>