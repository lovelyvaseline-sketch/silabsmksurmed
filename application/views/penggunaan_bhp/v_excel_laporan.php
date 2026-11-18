<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Penggunaan Bahan Habis Pakai (BHP)</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
        }

        .subtitle {
            font-size: 9.5pt;
            text-align: center;
            margin-bottom: 12px;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th {
            background-color: #0D9488;
            color: #ffffff;
            border: 1px solid #000000;
            padding: 6px 4px;
            text-align: center;
            font-weight: bold;
            font-size: 9.5pt;
        }

        table.data-table td {
            border: 1px solid #000000;
            padding: 5px 4px;
            vertical-align: middle;
            font-size: 9.5pt;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        table.ttd-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 25px;
        }

        table.ttd-table td {
            border: none !important;
            padding: 2px;
            font-size: 9.5pt;
        }
    </style>
</head>

<body>

    <!-- KOP HEADER LAPORAN -->
    <table style="width: 100%; border: none;">
        <tr>
            <td colspan="8" class="title">LAPORAN REKAPITULASI PENGGUNAAN BAHAN HABIS PAKAI (BHP)</td>
        </tr>
        <tr>
            <td colspan="8" class="title">SMK KESEHATAN SURYA MEDIKA</td>
        </tr>
        <tr>
            <td colspan="8" class="subtitle">
                <?= (!empty($tgl_mulai) && !empty($tgl_selesai))
                    ? 'Periode: ' . date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai))
                    : 'Semua Periode Penggunaan BHP'; ?>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA LAPORAN PENGGUNAAN BHP -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">NO</th>
                <th style="width: 110px;">ID TRANSAKSI</th>
                <th style="width: 160px;">NAMA BAHAN / BHP</th>
                <th style="width: 60px;">JUMLAH</th>
                <th style="width: 65px;">SATUAN</th>
                <th style="width: 150px;">PENGGUNA / PENGAJU</th>
                <th style="width: 90px;">TGL PAKAI</th>
                <th style="width: 90px;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_pakai = 0;
            if (!empty($laporan)):
                foreach ($laporan as $row):
                    $jumlah_pakai = (int) $row->jumlah_pakai;
                    $total_pakai += $jumlah_pakai;
            ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">#BHP-<?= str_pad($row->id_penggunaan, 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?= html_escape($row->nama_barang ?? '-'); ?></td>
                        <td class="text-center"><?= $jumlah_pakai; ?></td>
                        <td class="text-center"><?= html_escape($row->satuan ?? 'Pcs'); ?></td>
                        <td><?= html_escape($row->nama_pengguna ?? '-'); ?></td>
                        <td class="text-center"><?= !empty($row->created_at) ? date('d/m/Y', strtotime($row->created_at)) : '-'; ?></td>
                        <td class="text-center"><?= html_escape($row->status ?? 'Disetujui'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- BARIS REKAP TOTAL UNIT -->
                <tr>
                    <td colspan="3" class="bold text-left" style="background-color: #F3F4F6;">TOTAL BAHAN DIGUNAKAN:</td>
                    <td colspan="2" class="bold text-center" style="background-color: #F3F4F6;"><?= $total_pakai; ?> Item/Unit</td>
                    <td colspan="3" style="background-color: #F3F4F6;"></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="8" class="text-center">Data penggunaan BHP tidak ditemukan untuk periode ini.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br>

    <!-- LEMBAR TANDA TANGAN (KEPSEK & LABORAN) -->
    <table class="ttd-table">
        <tr>
            <td colspan="4" style="width: 45%; text-align: center; vertical-align: top;">
                Mengetahui,<br>
                <b>Kepala Sekolah SMK Kesehatan Surya Medika</b>
                <br><br><br><br><br>
                <u><b><?= html_escape($kepsek->nama_lengkap ?? 'Dr. H. Ahmad, M.Pd'); ?></b></u><br>
                NIP/NPY: <?= html_escape($kepsek->nip_nik_user ?? 'kepsek_ahmad'); ?>
            </td>
            <td style="width: 10%;"></td>
            <td colspan="4" style="width: 45%; text-align: center; vertical-align: top;">
                Singaraja, <?= date('d F Y'); ?><br>
                <b>Kepala Laboratorium Keperawatan</b>
                <br><br><br><br><br>
                <u><b><?= html_escape($laboran->nama_lengkap ?? 'Putu Ayu Masriani, S.Kep.'); ?></b></u><br>
                NIP/NPY: <?= html_escape($laboran->nip_nik_user ?? 'laboran_siti'); ?>
            </td>
        </tr>
    </table>

</body>

</html>