<?php
require_once '../db.php';
require_once 'gemini.php';

$consultationId = $_GET['id'] ?? null;

if (!$consultationId) {
    die("Consultation ID required");
}

try {
    $stmt = $pdo->prepare("
        SELECT c.*, p.name as patient_name, d.name as doctor_name, d.specialty
        FROM consultations c
        JOIN appointments a ON c.appointment_id = a.id
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        WHERE c.id = ?
    ");
    $stmt->execute([$consultationId]);
    $data = $stmt->fetch();

    if (!$data) {
        die("Consultation not found");
    }

    // Generate verification URL for QR
    $verificationUrl = "http://{$_SERVER['HTTP_HOST']}/medi_agenda/verify_prescription.php?id={$consultationId}";
    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=" . urlencode($verificationUrl);

} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Receta Médica - <?php echo htmlspecialchars($data['patient_name']); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; color: #333; }
        .header { border-bottom: 3px solid #2563eb; padding-bottom: 20px; margin-bottom: 30px; display: flex; justify-content: space-between; }
        .qr-section { text-align: center; border-left: 2px solid #e5e7eb; padding-left: 20px; }
        .qr-code { width: 120px; height: 120px; }
        h1 { margin: 0; color: #2563eb; font-size: 28px; }
        .clinic-info { margin-top: 10px; font-size: 12px; color: #64748b; }
        .patient-info { background: #f8fafc; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        .section { margin-bottom: 30px; }
        .section-title { font-weight: bold; color: #2563eb; font-size: 14px; margin-bottom: 10px; text-transform: uppercase; }
        .prescription-item { margin-bottom: 8px; line-height: 1.6; }
        .label { font-weight: bold; color: #475569; }
        .footer { margin-top: 50px; border-top: 2px solid #e5e7eb; padding-top: 20px; text-align: center; font-size: 11px; color: #94a3b8; }
    </style>
</head>
<body>
    <div class="header">
        <div>
            <h1>📋 RECETA MÉDICA</h1>
            <div class="clinic-info">
                <strong>Medi-Agenda AI</strong><br>
                Dr. <?php echo htmlspecialchars($data['doctor_name']); ?> - <?php echo htmlspecialchars($data['specialty']); ?><br>
                ID: #<?php echo $consultationId; ?> | Fecha: <?php echo date('d/m/Y H:i'); ?>
            </div>
        </div>
        <div class="qr-section">
            <img src="<?php echo $qrUrl; ?>" alt="QR Code" class="qr-code">
            <div style="font-size: 9px; color: #64748b; margin-top: 5px;">
                <strong>VERIFICACIÓN</strong><br>
                Escanea para validar
            </div>
        </div>
    </div>

    <div class="patient-info">
        <div style="font-size: 16px;"><span class="label">Paciente:</span> <?php echo htmlspecialchars($data['patient_name']); ?></div>
    </div>

    <div class="section">
        <div class="section-title">Diagnóstico</div>
        <div class="prescription-item"><?php echo nl2br(htmlspecialchars($data['diagnosis'])); ?></div>
    </div>

    <div class="section">
        <div class="section-title">Medicamentos y Dosificación</div>
        <div class="prescription-item"><?php echo nl2br(htmlspecialchars($data['medication'])); ?></div>
    </div>

    <?php if($data['notes']): ?>
    <div class="section">
        <div class="section-title">Instrucciones Adicionales</div>
        <div class="prescription-item"><?php echo nl2br(htmlspecialchars($data['notes'])); ?></div>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>© <?php echo date('Y'); ?> Medi-Agenda AI - Sistema Inteligente de Gestión Médica</p>
        <p style="font-size: 9px;">Esta receta es válida solo si el código QR verifica correctamente</p>
    </div>
</body>
</html>
