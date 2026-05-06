<?php
/**
 * index.php
 * Halaman utama sistem absensi karyawan.
 * Menangani logika absen masuk / pulang dan menampilkan riwayat hari ini.
 */

require_once __DIR__ . '/koneksi.php';

// ─────────────────────────────────────────────
//  Helper: Escape output (XSS prevention)
// ─────────────────────────────────────────────
function e(string $str): string
{
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ─────────────────────────────────────────────
//  Ambil semua karyawan untuk dropdown
// ─────────────────────────────────────────────
$db       = getDB();
$karyawan = $db->query('SELECT id, nama, jabatan FROM karyawan ORDER BY nama')->fetchAll();
$today    = date('Y-m-d');
$now      = date('H:i:s');

// ─────────────────────────────────────────────
//  Proses Form Absen
// ─────────────────────────────────────────────
$message = '';
$msgType = '';   // 'success' | 'danger' | 'warning'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action      = $_POST['action']      ?? '';
    $karyawanId  = filter_input(INPUT_POST, 'karyawan_id', FILTER_VALIDATE_INT);

    // Validasi input
    if (!$karyawanId || !in_array($action, ['masuk', 'pulang'], true)) {
        $message = '❌ Input tidak valid. Pilih karyawan dan tipe absen.';
        $msgType = 'danger';
    } else {
        // Cek apakah karyawan valid
        $stmtCek = $db->prepare('SELECT id, nama FROM karyawan WHERE id = ?');
        $stmtCek->execute([$karyawanId]);
        $karData = $stmtCek->fetch();

        if (!$karData) {
            $message = '❌ Karyawan tidak ditemukan.';
            $msgType = 'danger';
        } else {
            // Cek record absensi hari ini
            $stmtAbsen = $db->prepare(
                'SELECT id, jam_masuk, jam_pulang FROM absensi
                  WHERE karyawan_id = ? AND tanggal = ?'
            );
            $stmtAbsen->execute([$karyawanId, $today]);
            $absenRecord = $stmtAbsen->fetch();

            if ($action === 'masuk') {
                if ($absenRecord) {
                    $message = '⚠️ <strong>' . e($karData['nama']) . '</strong> sudah absen masuk hari ini pukul ' . e($absenRecord['jam_masuk']) . '.';
                    $msgType = 'warning';
                } else {
                    // Insert absen masuk
                    $stmtInsert = $db->prepare(
                        'INSERT INTO absensi (karyawan_id, tanggal, jam_masuk) VALUES (?, ?, ?)'
                    );
                    $stmtInsert->execute([$karyawanId, $today, $now]);
                    $message = '✅ <strong>' . e($karData['nama']) . '</strong> berhasil absen <strong>MASUK</strong> pukul ' . date('H:i') . '.';
                    $msgType = 'success';
                }
            } elseif ($action === 'pulang') {
                if (!$absenRecord) {
                    $message = '⚠️ <strong>' . e($karData['nama']) . '</strong> belum absen masuk hari ini.';
                    $msgType = 'warning';
                } elseif ($absenRecord['jam_pulang']) {
                    $message = '⚠️ <strong>' . e($karData['nama']) . '</strong> sudah absen pulang hari ini pukul ' . e($absenRecord['jam_pulang']) . '.';
                    $msgType = 'warning';
                } else {
                    // Update jam pulang
                    $stmtUpdate = $db->prepare(
                        'UPDATE absensi SET jam_pulang = ? WHERE id = ?'
                    );
                    $stmtUpdate->execute([$now, $absenRecord['id']]);
                    $message = '✅ <strong>' . e($karData['nama']) . '</strong> berhasil absen <strong>PULANG</strong> pukul ' . date('H:i') . '.';
                    $msgType = 'success';
                }
            }
        }
    }
}

// ─────────────────────────────────────────────
//  Ambil riwayat absensi hari ini
// ─────────────────────────────────────────────
$stmtHistory = $db->prepare(
    'SELECT k.nama, k.jabatan,
            a.jam_masuk, a.jam_pulang,
            CASE
                WHEN a.jam_masuk IS NOT NULL AND a.jam_pulang IS NOT NULL THEN "Selesai"
                WHEN a.jam_masuk IS NOT NULL THEN "Hadir"
                ELSE "Belum Absen"
            END AS status
       FROM karyawan k
  LEFT JOIN absensi a ON a.karyawan_id = k.id AND a.tanggal = ?
      ORDER BY k.nama'
);
$stmtHistory->execute([$today]);
$riwayat = $stmtHistory->fetchAll();

// ─────────────────────────────────────────────
//  Statistik hari ini
// ─────────────────────────────────────────────
$totalKaryawan = count($karyawan);
$totalHadir    = 0;
$totalPulang   = 0;
foreach ($riwayat as $r) {
    if ($r['jam_masuk'])  $totalHadir++;
    if ($r['jam_pulang']) $totalPulang++;
}
$totalBelum = $totalKaryawan - $totalHadir;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Absensi Karyawan – Catat kehadiran masuk dan pulang secara digital.">
    <title>Sistem Absensi Karyawan</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
          crossorigin="anonymous">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* ── Global ─────────────────────────────────────── */
        :root {
            --primary:   #4f46e5;
            --primary-d: #3730a3;
            --success:   #10b981;
            --warning:   #f59e0b;
            --danger:    #ef4444;
            --bg:        #f1f5f9;
            --card-bg:   #ffffff;
            --text:      #1e293b;
            --muted:     #64748b;
            --border:    #e2e8f0;
            --radius:    12px;
        }

        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── Topbar ─────────────────────────────────────── */
        .topbar {
            background: linear-gradient(135deg, var(--primary) 0%, #7c3aed 100%);
            padding: 1rem 0;
            box-shadow: 0 4px 20px rgba(79,70,229,.3);
        }
        .topbar h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #fff;
            margin: 0;
            letter-spacing: -0.3px;
        }
        .topbar .subtitle {
            font-size: .85rem;
            color: rgba(255,255,255,.75);
            margin: 0;
        }
        .topbar .clock {
            font-size: 1.3rem;
            font-weight: 600;
            color: #fff;
            font-variant-numeric: tabular-nums;
        }

        /* ── Stat Cards ─────────────────────────────────── */
        .stat-card {
            border: none;
            border-radius: var(--radius);
            padding: 1.25rem 1.5rem;
            color: #fff;
            box-shadow: 0 4px 14px rgba(0,0,0,.1);
            transition: transform .2s;
        }
        .stat-card:hover { transform: translateY(-3px); }
        .stat-card .stat-num  { font-size: 2rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-lbl  { font-size: .8rem; opacity: .85; margin-top: .25rem; }
        .stat-card .stat-icon { font-size: 2rem; opacity: .5; }

        .stat-total   { background: linear-gradient(135deg, #4f46e5, #7c3aed); }
        .stat-hadir   { background: linear-gradient(135deg, #10b981, #059669); }
        .stat-pulang  { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .stat-belum   { background: linear-gradient(135deg, #ef4444, #dc2626); }

        /* ── Card ───────────────────────────────────────── */
        .app-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 2px 12px rgba(0,0,0,.06);
            overflow: hidden;
        }
        .app-card .card-header-custom {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: .6rem;
            font-weight: 600;
            font-size: 1rem;
        }
        .app-card .card-body-custom { padding: 1.5rem; }

        /* ── Form ───────────────────────────────────────── */
        .form-label { font-weight: 500; font-size: .9rem; }

        .form-select, .form-control {
            border: 1.5px solid var(--border);
            border-radius: 8px;
            padding: .65rem 1rem;
            font-size: .95rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79,70,229,.15);
        }

        .btn-absen {
            border-radius: 8px;
            font-weight: 600;
            font-size: .95rem;
            padding: .65rem 1.4rem;
            transition: transform .15s, box-shadow .15s;
        }
        .btn-absen:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,.15);
        }
        .btn-masuk  { background: var(--primary); border-color: var(--primary); color: #fff; }
        .btn-masuk:hover  { background: var(--primary-d); border-color: var(--primary-d); color: #fff; }
        .btn-pulang { background: var(--warning); border-color: var(--warning); color: #fff; }
        .btn-pulang:hover { background: #d97706; border-color: #d97706; color: #fff; }

        /* ── Table ──────────────────────────────────────── */
        .table-absensi th {
            font-size: .8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: var(--muted);
            background: #f8fafc;
            border-bottom: 2px solid var(--border);
            padding: .75rem 1rem;
        }
        .table-absensi td {
            vertical-align: middle;
            padding: .85rem 1rem;
            font-size: .9rem;
            border-bottom: 1px solid var(--border);
        }
        .table-absensi tr:last-child td { border-bottom: none; }
        .table-absensi tr:hover td { background: #f8fafc; }

        .avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), #7c3aed);
            color: #fff;
            font-weight: 700;
            font-size: .9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        /* ── Badge Status ───────────────────────────────── */
        .badge-hadir   { background: #d1fae5; color: #065f46; }
        .badge-selesai { background: #dbeafe; color: #1e3a8a; }
        .badge-belum   { background: #fee2e2; color: #7f1d1d; }
        .badge-status {
            padding: .35rem .75rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
        }

        /* ── Alert ──────────────────────────────────────── */
        .alert { border-radius: 10px; font-size: .9rem; }

        /* ── Footer ─────────────────────────────────────── */
        footer {
            font-size: .8rem;
            color: var(--muted);
            padding: 1.5rem 0;
            text-align: center;
            margin-top: 2rem;
        }
    </style>
</head>
<body>

<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║              TOP BAR                     ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<header class="topbar">
    <div class="container">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div>
                <h1><i class="bi bi-person-check-fill me-2"></i>Sistem Absensi Karyawan</h1>
                <p class="subtitle">Catat kehadiran secara digital &amp; real-time</p>
            </div>
            <div class="text-end">
                <div class="clock" id="liveClock">00:00:00</div>
                <div style="font-size:.8rem;color:rgba(255,255,255,.7);">
                    <?= date('l, d F Y') ?>
                </div>
            </div>
        </div>
    </div>
</header>

<main class="container py-4">

    <!-- ── Statistik ───────────────────────────── -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card stat-total d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-num"><?= $totalKaryawan ?></div>
                    <div class="stat-lbl">Total Karyawan</div>
                </div>
                <i class="bi bi-people-fill stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-hadir d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-num"><?= $totalHadir ?></div>
                    <div class="stat-lbl">Sudah Hadir</div>
                </div>
                <i class="bi bi-box-arrow-in-right stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-pulang d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-num"><?= $totalPulang ?></div>
                    <div class="stat-lbl">Sudah Pulang</div>
                </div>
                <i class="bi bi-box-arrow-right stat-icon"></i>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card stat-belum d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-num"><?= $totalBelum ?></div>
                    <div class="stat-lbl">Belum Absen</div>
                </div>
                <i class="bi bi-clock-history stat-icon"></i>
            </div>
        </div>
    </div>

    <!-- ── Alert Notifikasi ─────────────────────── -->
    <?php if ($message): ?>
    <div class="alert alert-<?= e($msgType) ?> alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
        <?= $message ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- ── Form Absen ──────────────────────────── -->
    <div class="app-card mb-4">
        <div class="card-header-custom">
            <i class="bi bi-pencil-square text-primary"></i>
            Form Absensi
        </div>
        <div class="card-body-custom">
            <form method="POST" action="" id="formAbsen">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-6">
                        <label for="karyawan_id" class="form-label">Nama Karyawan</label>
                        <select name="karyawan_id" id="karyawan_id"
                                class="form-select" required>
                            <option value="" disabled selected>-- Pilih Karyawan --</option>
                            <?php foreach ($karyawan as $k): ?>
                            <option value="<?= e((string)$k['id']) ?>">
                                <?= e($k['nama']) ?> – <?= e($k['jabatan']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">Tipe Absen</label>
                        <div class="d-flex gap-2">
                            <button type="submit" name="action" value="masuk"
                                    id="btn-masuk"
                                    class="btn btn-absen btn-masuk flex-fill">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Absen Masuk
                            </button>
                            <button type="submit" name="action" value="pulang"
                                    id="btn-pulang"
                                    class="btn btn-absen btn-pulang flex-fill">
                                <i class="bi bi-box-arrow-right me-1"></i>Absen Pulang
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- ── Tabel Riwayat Hari Ini ──────────────── -->
    <div class="app-card">
        <div class="card-header-custom">
            <i class="bi bi-table text-primary"></i>
            Riwayat Absensi Hari Ini
            <span class="ms-auto badge bg-primary rounded-pill"><?= date('d/m/Y') ?></span>
        </div>
        <div class="card-body-custom p-0">
            <?php if (empty($riwayat)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-inbox" style="font-size:2.5rem;"></i>
                    <p class="mt-2 mb-0">Belum ada data absensi hari ini.</p>
                </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-absensi mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Karyawan</th>
                            <th>Jabatan</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($riwayat as $i => $row): ?>
                        <?php
                            $inisial = strtoupper(substr($row['nama'], 0, 1));
                            $statusClass = match($row['status']) {
                                'Hadir'   => 'badge-hadir',
                                'Selesai' => 'badge-selesai',
                                default   => 'badge-belum',
                            };
                        ?>
                        <tr>
                            <td class="text-muted"><?= $i + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar"><?= e($inisial) ?></div>
                                    <span class="fw-500"><?= e($row['nama']) ?></span>
                                </div>
                            </td>
                            <td class="text-muted"><?= e($row['jabatan']) ?></td>
                            <td>
                                <?php if ($row['jam_masuk']): ?>
                                    <span class="text-success fw-600">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= e(substr($row['jam_masuk'], 0, 5)) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">–</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($row['jam_pulang']): ?>
                                    <span class="text-warning fw-600">
                                        <i class="bi bi-clock me-1"></i>
                                        <?= e(substr($row['jam_pulang'], 0, 5)) ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-muted">–</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status <?= $statusClass ?>">
                                    <?= e($row['status']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<footer>
    &copy; <?= date('Y') ?> Sistem Absensi Karyawan &mdash; Tugas Web Server
</footer>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc4s9bIOgUxi8T/jzmH9P0FbhB5BbBBnmkHbMSEKEQ7P"
        crossorigin="anonymous"></script>

<script>
    // ── Live Clock ──────────────────────────────────────
    function updateClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        document.getElementById('liveClock').textContent =
            `${pad(now.getHours())}:${pad(now.getMinutes())}:${pad(now.getSeconds())}`;
    }
    updateClock();
    setInterval(updateClock, 1000);

    // ── Konfirmasi sebelum submit ────────────────────────
    document.getElementById('formAbsen').addEventListener('submit', function(e) {
        const select = document.getElementById('karyawan_id');
        if (!select.value) {
            e.preventDefault();
            alert('Harap pilih nama karyawan terlebih dahulu!');
        }
    });
</script>
</body>
</html>
