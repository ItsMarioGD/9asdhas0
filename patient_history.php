<?php 
include 'includes/header.php'; 
require_once 'db.php';

$patientId = $_GET['id'] ?? null;
if (!$patientId) {
    echo "<script>window.location.href='patients.php';</script>";
    exit;
}

// Get Patient Info
$stmt = $pdo->prepare("SELECT * FROM patients WHERE id = ?");
$stmt->execute([$patientId]);
$patient = $stmt->fetch();

if (!$patient) {
    echo "<div class='text-center p-10'>Paciente no encontrado</div>";
    exit;
}

// Get History (Appointments + Consultations)
$stmt = $pdo->prepare("
    SELECT a.id as appointment_id, a.date_time, a.reason, a.status, a.appointment_type,
           c.id as consultation_id, c.diagnosis, c.medication, c.notes,
           d.name as doctor_name
    FROM appointments a
    LEFT JOIN consultations c ON a.id = c.appointment_id
    LEFT JOIN doctors d ON a.doctor_id = d.id
    WHERE a.patient_id = ?
    ORDER BY a.date_time DESC
");
$stmt->execute([$patientId]);
$history = $stmt->fetchAll();
?>

<div class="max-w-5xl mx-auto">
    <!-- Back Button -->
    <a href="patients.php" class="inline-flex items-center gap-2 text-gray-500 hover:text-blue-600 mb-6 font-bold transition">
        <i class="fa-solid fa-arrow-left"></i> Volver al Directorio
    </a>

    <!-- Patient Header Card -->
    <div class="glass-panel rounded-3xl p-8 mb-10 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-blue-500/20 to-purple-500/20 rounded-full blur-3xl -mr-16 -mt-16"></div>
        
        <div class="flex flex-col md:flex-row items-center gap-8 relative z-10">
            <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 flex items-center justify-center text-white text-5xl font-bold shadow-2xl border-4 border-white/20">
                <?php echo strtoupper(substr($patient['name'], 0, 1)); ?>
            </div>
            <div class="text-center md:text-left flex-1">
                <h1 class="text-4xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($patient['name']); ?></h1>
                <div class="flex flex-wrap justify-center md:justify-start gap-4 text-gray-600">
                    <span class="flex items-center gap-2 bg-white/50 px-4 py-2 rounded-full">
                        <i class="fa-solid fa-phone text-blue-600"></i> <?php echo htmlspecialchars($patient['phone'] ?? 'Sin teléfono'); ?>
                    </span>
                    <span class="flex items-center gap-2 bg-white/50 px-4 py-2 rounded-full">
                        <i class="fa-solid fa-hashtag text-blue-600"></i> ID: <?php echo $patient['id']; ?>
                    </span>
                </div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-blue-600"><?php echo count($history); ?></div>
                <div class="text-sm text-gray-500 font-bold uppercase tracking-wide">Visitas Totales</div>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="relative pl-8 md:pl-0">
        <!-- Vertical Line -->
        <div class="absolute left-8 md:left-1/2 top-0 bottom-0 w-1 bg-blue-100 transform -translate-x-1/2 hidden md:block"></div>
        <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-100 md:hidden"></div>

        <div class="space-y-12">
            <?php foreach ($history as $index => $item): 
                $isLeft = $index % 2 == 0;
                $date = new DateTime($item['date_time']);
                $hasConsultation = !empty($item['consultation_id']);
            ?>
            <div class="relative flex items-center justify-between md:justify-normal <?php echo $isLeft ? 'md:flex-row-reverse' : ''; ?>">
                
                <!-- Dot -->
                <div class="absolute left-0 md:left-1/2 w-4 h-4 rounded-full border-4 border-white shadow-lg transform -translate-x-1/2 z-10 
                    <?php echo $hasConsultation ? 'bg-green-500' : 'bg-blue-500'; ?>"></div>

                <!-- Spacer for Desktop -->
                <div class="hidden md:block w-1/2"></div>

                <!-- Content Card -->
                <div class="w-full md:w-1/2 pl-8 md:pl-0 <?php echo $isLeft ? 'md:pr-12' : 'md:pl-12'; ?>">
                    <div class="glass-panel p-6 rounded-2xl hover:shadow-xl transition group border-l-4 <?php echo $hasConsultation ? 'border-l-green-500' : 'border-l-blue-500'; ?>">
                        
                        <!-- Header -->
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <div class="text-sm font-bold text-gray-400 uppercase tracking-wide mb-1">
                                    <?php echo $date->format('d M Y, h:i A'); ?>
                                </div>
                                <h3 class="text-xl font-bold text-gray-900">
                                    <?php echo htmlspecialchars($item['appointment_type'] ?? 'Consulta'); ?>
                                </h3>
                                <p class="text-sm text-blue-600 font-semibold">Dr. <?php echo htmlspecialchars($item['doctor_name']); ?></p>
                            </div>
                            <?php if ($hasConsultation): ?>
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fa-solid fa-check-circle mr-1"></i> Completada
                                </span>
                            <?php else: ?>
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-xs font-bold">
                                    <i class="fa-solid fa-clock mr-1"></i> <?php echo ucfirst($item['status']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Details -->
                        <div class="space-y-3">
                            <div class="bg-gray-50 p-3 rounded-xl">
                                <span class="text-xs font-bold text-gray-400 uppercase block mb-1">Motivo</span>
                                <p class="text-gray-700"><?php echo htmlspecialchars($item['reason']); ?></p>
                            </div>

                            <?php if ($hasConsultation): ?>
                                <div class="bg-green-50 p-3 rounded-xl border border-green-100">
                                    <span class="text-xs font-bold text-green-600 uppercase block mb-1">Diagnóstico</span>
                                    <p class="text-gray-800 font-medium"><?php echo htmlspecialchars($item['diagnosis']); ?></p>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <a href="api/generate_prescription.php?id=<?php echo $item['consultation_id']; ?>" target="_blank" 
                                       class="inline-flex items-center gap-2 text-blue-600 font-bold hover:text-blue-800 transition">
                                        <i class="fa-solid fa-file-prescription"></i> Ver Receta
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
