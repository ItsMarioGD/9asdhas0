<?php
require_once 'db.php';

$consultationId = $_GET['id'] ?? null;

if (!$consultationId) {
    die("ID no proporcionado");
}

try {
    $stmt = $pdo->prepare("
        SELECT c.*, p.name as patient_name, d.name as doctor_name, d.specialty, a.date_time
        FROM consultations c
        JOIN appointments a ON c.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE c.id = ?
    ");
    $stmt->execute([$consultationId]);
    $data = $stmt->fetch();

    $isValid = ($data !== false);

} catch (Exception $e) {
    $isValid = false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación de Receta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-2xl p-8">
        <?php if ($isValid): ?>
            <div class="text-center">
                <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-check-circle text-5xl text-green-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">✅ Receta Válida</h1>
                <p class="text-gray-600 mb-6">Esta receta médica es auténtica</p>
                
                <div class="bg-gray-50 rounded-2xl p-6 text-left space-y-3">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Paciente</div>
                        <div class="text-lg font-semibold"><?php echo htmlspecialchars($data['patient_name']); ?></div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Doctor</div>
                        <div class="text-lg">Dr. <?php echo htmlspecialchars($data['doctor_name']); ?></div>
                        <div class="text-sm text-gray-600"><?php echo htmlspecialchars($data['specialty']); ?></div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">Fecha de Consulta</div>
                        <div class="text-lg"><?php echo date('d/m/Y H:i', strtotime($data['date_time'])); ?></div>
                    </div>
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase">ID de Consulta</div>
                        <div class="text-lg font-mono">#<?php echo $consultationId; ?></div>
                    </div>
                </div>

                <a href="api/generate_prescription.php?id=<?php echo $consultationId; ?>" target="_blank" class="mt-6 block w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">
                    <i class="fa-solid fa-file-pdf mr-2"></i>Ver Receta Completa
                </a>
            </div>
        <?php else: ?>
            <div class="text-center">
                <div class="w-24 h-24 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fa-solid fa-times-circle text-5xl text-red-600"></i>
                </div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">❌ Receta No Válida</h1>
                <p class="text-gray-600 mb-6">Esta receta no existe en nuestro sistema o ha sido alterada</p>
                
                <div class="bg-red-50 border border-red-200 rounded-2xl p-6 text-left">
                    <p class="text-red-800 font-semibold">⚠️ Advertencia</p>
                    <p class="text-sm text-red-700 mt-2">
                        Si recibiste esta receta de un profesional médico, por favor contacta a la clínica para verificar su autenticidad.
                    </p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="mt-8 text-center text-xs text-gray-500">
            <p>© <?php echo date('Y'); ?> Medi-Agenda AI</p>
            <p class="mt-1">Sistema de Verificación de Recetas Médicas</p>
        </div>
    </div>
</body>
</html>
