<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Data Pengguna</title>
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

        .uppercase {
            text-transform: uppercase;
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

    <!-- KOP HEADER -->
    <table style="width: 100%; border: none;">
        <tr>
            <td colspan="7" class="title">LAPORAN REKAPITULASI DATA PENGGUNA (USERS)</td>
        </tr>
        <tr>
            <td colspan="7" class="title">SMK KESEHATAN SURYA MEDIKA</td>
        </tr>
        <tr>
            <td colspan="7" class="subtitle">
                <?= (!empty($filter_role) && $filter_role !== 'Semua') ? 'Role: ' . strtoupper($filter_role) . ' | ' : 'Semua Role | '; ?>
                Tanggal Cetak: <?= date('d/m/Y'); ?>
            </td>
        </tr>
    </table>

    <!-- TABEL DATA USER -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 35px;">NO</th>
                <th style="width: 140px;">NIP / NIK</th>
                <th style="width: 180px;">NAMA LENGKAP</th>
                <th style="width: 120px;">USERNAME</th>
                <th style="width: 180px;">EMAIL</th>
                <th style="width: 90px;">ROLE</th>
                <th style="width: 80px;">STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            if (!empty($users)):
                foreach ($users as $u):
            ?>
                    <tr>
                        <td class="text-center"><?= $no++; ?></td>
                        <td class="text-center">'<?= html_escape($u->nip_nik_user); ?></td>
                        <td><?= html_escape($u->nama_lengkap); ?></td>
                        <td><?= html_escape($u->username); ?></td>
                        <td><?= html_escape($u->email); ?></td>
                        <td class="text-center uppercase font-bold"><?= html_escape($u->role); ?></td>
                        <td class="text-center"><?= ($u->status_aktif == 1) ? 'Aktif' : 'Nonaktif'; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="5" class="bold text-left" style="background-color: #F3F4F6;">TOTAL PENGGUNA:</td>
                    <td colspan="2" class="bold text-center" style="background-color: #F3F4F6;"><?= count($users); ?> Akun</td>
                </tr>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="text-center">Data pengguna tidak ditemukan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>

</html>