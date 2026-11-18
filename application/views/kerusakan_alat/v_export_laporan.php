<div class="p-6">
    <!-- Header Form -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Export Laporan Kerusakan Alat</h2>
            <p class="text-xs text-slate-500">Unduh berkas rekapitulasi data insiden kerusakan instrumen praktikum format Microsoft Excel.</p>
        </div>
        <a href="<?= site_url('kerusakan_alat'); ?>" class="bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-semibold px-4 py-2.5 rounded-xl transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Card Form Filter -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 max-w-2xl">
        <form action="<?= site_url('kerusakan_alat/proses_export_excel'); ?>" method="GET" target="_blank" class="space-y-5">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <!-- Tanggal Mulai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL LAPORAN (MULAI)</label>
                    <input type="date" name="tgl_mulai" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>

                <!-- Tanggal Selesai -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">TANGGAL LAPORAN (SELESAI)</label>
                    <input type="date" name="tgl_selesai" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 text-slate-800">
                </div>
            </div>

            <!-- Filter Status Perbaikan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">FILTER STATUS PERBAIKAN</label>
                <select name="status_perbaikan" class="w-full px-3.5 py-2.5 text-xs bg-white border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-teal-500 font-semibold text-slate-800 cursor-pointer">
                    <option value="Semua">-- Semua Status Perbaikan --</option>
                    <option value="Menunggu Perbaikan">Menunggu Perbaikan</option>
                    <option value="Sedang Diperbaiki">Sedang Diperbaiki</option>
                    <option value="Selesai Diperbaiki">Selesai Diperbaiki</option>
                    <option value="Afkir/Diganti">Afkir / Diganti</option>
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