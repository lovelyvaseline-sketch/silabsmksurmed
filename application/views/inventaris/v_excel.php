<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title><?= $title; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .font-bold {
            font-weight: bold;
        }

        .header-title {
            font-size: 12pt;
            font-weight: bold;
            text-align: center;
        }

        .header-subtitle {
            font-size: 11pt;
            font-weight: bold;
            text-align: center;
        }

        .header-info {
            font-size: 8pt;
            text-align: center;
            color: #333333;
        }

        /* Style Tabel Utama */
        table.table-data {
            border-collapse: collapse;
            width: 100%;
        }

        table.table-data th {
            background-color: #008080;
            /* Warna Teal sesuai gambar */
            color: #FFFFFF;
            font-weight: bold;
            font-size: 9pt;
            text-align: center;
            vertical-align: middle;
            border: 1px solid #000000;
            padding: 6px;
        }

        table.table-data td {
            border: 1px solid #000000;
            padding: 5px;
            font-size: 9pt;
            vertical-align: middle;
        }

        /* Format Teks Barcode sebagai String murni agar angka 0 di depan tidak hilang */
        .str-format {
            mso-number-format: "\@";
        }
    </style>
</head>

<body>

    <!-- KOP HEADER LAPORAN -->
    <table width="100%">
        <tr>
            <td colspan="9" class="header-title">SMK KESEHATAN SURYA MEDIKA</td>
        </tr>
        <tr>
            <td colspan="9" class="header-subtitle"><?= strtoupper($title ?? 'LAPORAN STOK INVENTARIS'); ?></td>
        </tr>
        <tr>
            <td colspan="9" class="header-info">
                <?php
                // Fallback otomatis jika variabel dari Controller belum terkirim
                $tgl_info  = isset($tgl_cetak) ? $tgl_cetak : date('d/m/Y H:i:s');
                $nama_info = isset($nama_cetak) ? $nama_cetak : ($this->session->userdata('nama_lengkap') ?? 'Operator Sekolah');
                ?>
                Tanggal Cetak: <?= $tgl_info; ?> | Dicetak Oleh: <?= html_escape($nama_info); ?>
            </td>
        </tr>
        <tr>
            <td colspan="9"></td>
        </tr>
    </table>
    <!-- TABEL DATA INVENTARIS -->
    <table class="table-data">
        <thead>
            <tr>
                <th width="5%">NO</th>
                <th width="14%">KODE BARCODE</th>
                <th width="25%">NAMA BARANG</th>
                <th width="20%">KATEGORI</th>
                <th width="7%">STOK</th>
                <th width="8%">SATUAN</th>
                <th width="15%">TANGGAL PENGADAAN</th>
                <th width="8%">KONDISI</th>
                <th width="25%">LOKASI PENYIMPANAN</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($inventaris)): $no = 1;
                foreach ($inventaris as $item): ?>
                    <?php
                    // Format Tanggal Pengadaan
                    $tgl_pengadaan = !empty($item->tgl_pengadaan) ? date('d/m/Y', strtotime($item->tgl_pengadaan)) : '-';
                    $lokasi        = !empty($item->lokasi_penyimpanan) ? $item->lokasi_penyimpanan : 'Ruang Penyimpanan Keperawatan';
                    ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center str-format"><?= html_escape($item->kode_barcode); ?></td>
                        <td class="text-left"><?= html_escape($item->nama_barang); ?></td>
                        <td class="text-left"><?= html_escape($item->kategori); ?></td>
                        <td class="text-center font-bold"><?= $item->stok; ?></td>
                        <td class="text-left"><?= html_escape($item->satuan); ?></td>
                        <td class="text-center"><?= $tgl_pengadaan; ?></td>
                        <td class="text-center"><?= html_escape($item->kondisi); ?></td>
                        <td class="text-left"><?= html_escape($lokasi); ?></td>
                    </tr>
                <?php endforeach;
            else: ?>
                <tr>
                    <td colspan="9" class="text-center">Belum ada data inventaris.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <br><br>

    <!-- BLOK TANDA TANGAN GANDA (DINAMIS DARI DB TB_USERS) -->
    <table width="100%">
        <tr>
            <!-- SISI KIRI: KEPALA SEKOLAH -->
            <td colspan="4" class="text-center" style="vertical-align: top;">
                Mengetahui,<br>
                <b>Kepala Sekolah SMK Kesehatan Surya Medika</b>
                <br><br><br><br><br>

                <!-- NAMA & NIP/NIK KEPSEK DARI DB -->
                <u><b><?= html_escape($kepsek->nama_lengkap ?? 'Nyoman Ayu Sri Desi Ariani, S.Pd.,M.Pd.'); ?></b></u><br>
                NIP/NPY: <?= html_escape($kepsek->nip_nik_user ?? '2016.06.120'); ?>
            </td>

            <!-- SPASI TENGAH -->
            <td colspan="1"></td>

            <!-- SISI KANAN: KEPALA LAB / LABORAN -->
            <td colspan="4" class="text-center" style="vertical-align: top;">
                Singaraja, <?= date('d F Y'); ?><br>
                <b>Kepala Laboratorium Keperawatan</b>
                <br><br><br><br><br>

                <!-- NAMA & NIP/NIK LABORAN DARI DB -->
                <u><b><?= html_escape($laboran->nama_lengkap ?? 'Desy Natalia Luis, S.Tr.Keb.'); ?></b></u><br>
                NIP/NPY: <?= html_escape($laboran->nip_nik_user ?? '2026.21.313'); ?>
            </td>
        </tr>
    </table>

</body>

</html>