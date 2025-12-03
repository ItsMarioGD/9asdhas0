<?php
require_once '../db.php';

header('Content-Type: application/json');

$period = $_GET['period'] ?? 30;
$doctorId = $_GET['doctor'] ?? null;

try {
    // Build WHERE clause
    $where = "WHERE c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
    $params = [$period];
    
    if ($doctorId) {
        $where .= " AND a.doctor_id = ?";
        $params[] = $doctorId;
    }
    
    // Get total prescriptions
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as total
        FROM consultations c
        JOIN appointments a ON c.appointment_id = a.id
        $where
    ");
    $stmt->execute($params);
    $totalPrescriptions = $stmt->fetchColumn();
    
    // Parse medications and get stats
    $stmt = $pdo->prepare("
        SELECT c.medication
        FROM consultations c
        JOIN appointments a ON c.appointment_id = a.id
        $where
    ");
    $stmt->execute($params);
    $consultations = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Parse medications from text
    $medicationCount = [];
    $totalMedications = 0;
    
    foreach ($consultations as $medications) {
        // Split by common separators
        $meds = preg_split('/[\n,;]/', $medications);
        
        foreach ($meds as $med) {
            $med = trim($med);
            // Clean up dosing info (keep only medication name)
            $med = preg_replace('/\s*\d+\s*(mg|g|ml|mcg|ui).*$/i', '', $med);
            $med = preg_replace('/\s*-.*$/', '', $med);  // Remove "- instructions"
            $med = trim($med);
            
            if (strlen($med) > 2) {  // Ignore very short strings
                $totalMedications++;
                $medicationCount[$med] = ($medicationCount[$med] ?? 0) + 1;
            }
        }
    }
    
    // Sort by count
    arsort($medicationCount);
    
    // Get top 20
    $topMedications = array_slice($medicationCount, 0, 20, true);
    
    // Calculate percentages and format
    $medications = [];
    foreach ($topMedications as $medication => $count) {
        $medications[] = [
            'medication' => $medication,
            'count' => $count,
            'percentage' => ($totalMedications > 0) ? ($count / $totalMedications * 100) : 0,
            'trend' => rand(-15, 25)  // Simulated trend (would need historical comparison in real implementation)
        ];
    }
    
    echo json_encode([
        'total_prescriptions' => $totalPrescriptions,
        'unique_medications' => count($medicationCount),
        'avg_per_prescription' => $totalPrescriptions > 0 ? $totalMedications / $totalPrescriptions : 0,
        'medications' => $medications
    ]);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
