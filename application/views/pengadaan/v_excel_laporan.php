<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Pengadaan Alat & BHP</title>
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

        .text-right {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .bold {
            font-weight: bold;
        }

        /* Styling Lembar Tanda Tangan */
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
            <td colspan="9" class="title">LAPORAN PENGAJUAN PENGADAAN ALAT & BAHAN HABIS PAKAI (BHP)</td>
        </tr>
        <tr>
            <td colspan="9" class="title">SMK KESEHATAN SURYA MEDIKA</td>
        </tr>
        <tr>
            <td colspan="9" class="subtitle">
                <?= (!empty($tgl_mulai) && !empty($tgl_selesai))
                    ? 'Periode: ' . date('d/m/Y', strtotime($tgl_mulai)) . ' s/d ' . date('d/m/Y', strtotime($tgl_selesai))
                    : 'Semua Periode Pengadaan'; ?>
            </td>
        </tr>
    </table>

    <!-- TABEL UTAMA DATA LAPORAN -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">NO</th>
                <th style="width: 110px;">ID PENGADAAN</th>
                <th style="width: 160px;">NAMA ITEM / BARANG</th>
                <th style="width: 60px;">JUMLAH</th>
                <th style="width: 65px;">SATUAN</th>
                <th style="width: 140px;">ESTIMASI HARGA TOTAL</th>
                <th style="width: 150px;">PENGAJU USULAN</th>
                <th style="width: 90px;">STATUS</th>
                <th style="width: 220px;">ALASAN PENGADAAN</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $total_anggaran = 0;
            if (!empty($laporan)):
                foreach ($laporan as $row):
                    $total_anggaran += (float) ($row->estimasi_harga ?? 0);
            ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">#<?= str_pad($row->id_pengadaan, 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?= html_escape($row->nama_item); ?></td>
                        <td class="text-center"><?= $row->jumlah; ?></td>
                        <td class="text-center"><?= html_escape($row->satuan ?? 'Pcs'); ?></td>
                        <td class="text-right">Rp <?= number_format($row->estimasi_harga ?? 0, 0, ',', '.'); ?></td>
                        <td><?= html_escape($row->nama_pengaju ?? '-'); ?></td>
                        <td class="text-center"><?= html_escape($row->status_approval ?? 'Pending'); ?></td>
                        <td><?= html_escape($row->alasan_pengadaan ?? '-'); ?></td>
                    </tr>
                <?php endforeach; ?>
                <!-- BARIS TOTAL ESTIMASI ANGGARAN -->
                <tr>
                    <td colspan="5" class="bold text-left">TOTAL ESTIMASI ANGGARAN:</td>
                    <td class="bold text-right">Rp <?= number_format($total_anggaran, 0, ',', '.'); ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center">Data pengadaan tidak ditemukan untuk periode ini.</td>
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