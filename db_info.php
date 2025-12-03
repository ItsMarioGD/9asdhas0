<?php 
require_once 'db.php';
$dbInfo = getDatabaseInfo();

// Get database statistics
try {
    $stats = [
        'doctors' => $pdo->query("SELECT COUNT(*) FROM doctors")->fetchColumn(),
        'patients' => $pdo->query("SELECT COUNT(*) FROM patients")->fetchColumn(),
        'appointments' => $pdo->query("SELECT COUNT(*) FROM appointments")->fetchColumn(),
        'consultations' => $pdo->query("SELECT COUNT(*) FROM consultations")->fetchColumn(),
    ];
} catch (Exception $e) {
    $stats = ['error' => $e->getMessage()];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Info - Medi-Agenda</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="static/css/style.css" rel="stylesheet">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    
    <div class="max-w-4xl w-full">
        <div class="bg-white rounded-3xl shadow-2xl p-8">
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center">
                    <i class="fa-solid fa-database text-3xl text-white"></i>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Database Information</h1>
                    <p class="text-gray-500">System Configuration & Statistics</p>
                </div>
            </div>

            <!-- Database Type -->
            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-600 mb-1">Database Type</p>
                        <h2 class="text-2xl font-bold text-gray-900"><?php echo $dbInfo['type']; ?></h2>
                    </div>
                    <?php if ($dbInfo['type'] == 'MySQL (Server)'): ?>
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-server text-2xl text-green-600"></i>
                        </div>
                    <?php else: ?>
                        <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                            <i class="fa-solid fa-file-code text-2xl text-purple-600"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Connection Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-gray-50 rounded-2xl p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-2">Host</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo $dbInfo['host']; ?></p>
                </div>
                <div class="bg-gray-50 rounded-2xl p-6">
                    <p class="text-xs font-bold text-gray-400 uppercase mb-2">Database</p>
                    <p class="text-lg font-semibold text-gray-900"><?php echo $dbInfo['database']; ?></p>
                </div>
            </div>

            <?php if ($dbInfo['file']): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-6 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fa-solid fa-folder-open text-yellow-600 text-xl mt-1"></i>
                    <div>
                        <p class="text-sm font-bold text-yellow-800 mb-1">File Location</p>
                        <p class="text-sm text-yellow-700 font-mono break-all"><?php echo $dbInfo['file']; ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Statistics -->
            <div class="border-t border-gray-200 pt-6 mb-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Database Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <i class="fa-solid fa-user-doctor text-2xl text-blue-600 mb-2"></i>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $stats['doctors'] ?? 0; ?></p>
                        <p class="text-xs text-gray-600 font-semibold">Doctors</p>
                    </div>
                    <div class="bg-green-50 rounded-xl p-4 text-center">
                        <i class="fa-solid fa-users text-2xl text-green-600 mb-2"></i>
                        <p class="text-3xl font-bold text-green-600"><?php echo $stats['patients'] ?? 0; ?></p>
                        <p class="text-xs text-gray-600 font-semibold">Patients</p>
                    </div>
                    <div class="bg-purple-50 rounded-xl p-4 text-center">
                        <i class="fa-solid fa-calendar text-2xl text-purple-600 mb-2"></i>
                        <p class="text-3xl font-bold text-purple-600"><?php echo $stats['appointments'] ?? 0; ?></p>
                        <p class="text-xs text-gray-600 font-semibold">Appointments</p>
                    </div>
                    <div class="bg-orange-50 rounded-xl p-4 text-center">
                        <i class="fa-solid fa-notes-medical text-2xl text-orange-600 mb-2"></i>
                        <p class="text-3xl font-bold text-orange-600"><?php echo $stats['consultations'] ?? 0; ?></p>
                        <p class="text-xs text-gray-600 font-semibold">Consultations</p>
                    </div>
                </div>
            </div>

            <!-- Features Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6 mb-6">
                <h3 class="text-lg font-bold text-blue-900 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-circle-info"></i>
                    How It Works
                </h3>
                <div class="space-y-2 text-sm text-blue-800">
                    <p><strong>🔄 Auto-Detection:</strong> The system automatically detects which database to use:</p>
                    <ul class="list-disc list-inside ml-4 space-y-1">
                        <li><strong>MySQL:</strong> If <code>.env</code> file exists with DB credentials</li>
                        <li><strong>SQLite:</strong> Falls back to local file if MySQL unavailable</li>
                    </ul>
                    <p class="mt-3"><strong>📦 Portability:</strong> SQLite mode makes the entire app portable - just copy the folder!</p>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex gap-4">
                <a href="index.php" class="flex-1 py-3 bg-blue-600 text-white rounded-xl font-bold text-center hover:bg-blue-700 transition">
                    <i class="fa-solid fa-home mr-2"></i>Go to Application
                </a>
                <?php if ($dbInfo['type'] == 'SQLite (Local File)'): ?>
                <a href="?download=db" class="flex-1 py-3 bg-green-600 text-white rounded-xl font-bold text-center hover:bg-green-700 transition">
                    <i class="fa-solid fa-download mr-2"></i>Download DB File
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-600 text-sm">
            <p>© 2025 Medi-Agenda AI - Hybrid Database System</p>
        </div>
    </div>

    <?php
    // Handle database download
    if (isset($_GET['download']) && $_GET['download'] == 'db' && $dbInfo['file']) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="medi_agenda_backup_' . date('Y-m-d_H-i-s') . '.db"');
        readfile($dbInfo['file']);
        exit;
    }
    ?>
</body>
</html>
