<?php include 'includes/header.php'; 
require_once 'db.php';
?>

<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div class="flex items-center gap-4">
            <div class="p-3 bg-white rounded-2xl shadow-sm">
                <i class="fa-solid fa-pills text-3xl text-purple-600"></i>
            </div>
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Estadísticas de Medicamentos</h2>
                <p class="text-gray-500">Análisis de prescripciones para doctores y visitadores médicos</p>
            </div>
        </div>
        <button onclick="exportMedicationReport()" class="px-6 py-3 bg-green-600 text-white rounded-xl font-bold shadow-lg hover:bg-green-700 transition">
            <i class="fa-solid fa-file-excel mr-2"></i>Exportar Reporte
        </button>
    </div>

    <!-- Filter Card -->
    <div class="glass-panel rounded-3xl p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Período</label>
                <select id="period" onchange="loadMedicationStats()" class="w-full rounded-xl border-gray-200 py-3 px-4">
                    <option value="7">Última semana</option>
                    <option value="30" selected>Último mes</option>
                    <option value="90">Últimos 3 meses</option>
                    <option value="365">Último año</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Doctor</label>
                <select id="doctor_filter" onchange="loadMedicationStats()" class="w-full rounded-xl border-gray-200 py-3 px-4">
                    <option value="">Todos los doctores</option>
                    <!-- Populated by JS -->
                </select>
            </div>
            <div class="col-span-2 flex items-end">
                <button onclick="loadMedicationStats()" class="w-full py-3 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition">
                    <i class="fa-solid fa-sync mr-2"></i>Actualizar
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 font-semibold text-sm">Total Recetas</p>
                    <p id="total-prescriptions" class="text-4xl font-bold text-blue-600">0</p>
                </div>
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-file-prescription text-2xl text-blue-600"></i>
                </div>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 font-semibold text-sm">Medicamentos Únicos</p>
                    <p id="unique-medications" class="text-4xl font-bold text-purple-600">0</p>
                </div>
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-pills text-2xl text-purple-600"></i>
                </div>
            </div>
        </div>
        <div class="glass-card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 font-semibold text-sm">Promedio por Receta</p>
                    <p id="avg-per-prescription" class="text-4xl font-bold text-green-600">0</p>
                </div>
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-chart-line text-2xl text-green-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Medications Table -->
    <div class="glass-panel rounded-3xl p-8">
        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fa-solid fa-trophy text-yellow-500"></i>
            Top 20 Medicamentos Más Recetados
        </h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b-2 border-gray-200">
                        <th class="text-left py-4 px-4 font-bold text-gray-700">#</th>
                        <th class="text-left py-4 px-4 font-bold text-gray-700">Medicamento</th>
                        <th class="text-center py-4 px-4 font-bold text-gray-700">Veces Recetado</th>
                        <th class="text-center py-4 px-4 font-bold text-gray-700">% del Total</th>
                        <th class="text-left py-4 px-4 font-bold text-gray-700">Tendencia</th>
                    </tr>
                </thead>
                <tbody id="medications-tbody">
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-500">
                            <i class="fa-solid fa-circle-notch fa-spin text-4xl mb-4 block"></i>
                            Cargando datos...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
let allDoctors = [];

async function loadDoctors() {
    try {
        const res = await fetch('api/get_doctors_list.php');
        allDoctors = await res.json();
        
        const select = document.getElementById('doctor_filter');
        allDoctors.forEach(d => {
            const option = document.createElement('option');
            option.value = d.id;
            option.textContent = `Dr. ${d.name}`;
            select.appendChild(option);
        });
    } catch (e) {
        console.error('Error loading doctors:', e);
    }
}

async function loadMedicationStats() {
    const period = document.getElementById('period').value;
    const doctor = document.getElementById('doctor_filter').value;
    
    try {
        const res = await fetch(`api/medication_stats.php?period=${period}&doctor=${doctor}`);
        const data = await res.json();
        
        // Update stats
        document.getElementById('total-prescriptions').textContent = data.total_prescriptions;
        document.getElementById('unique-medications').textContent = data.unique_medications;
        document.getElementById('avg-per-prescription').textContent = data.avg_per_prescription.toFixed(1);
        
        // Update table
        const tbody = document.getElementById('medications-tbody');
        
        if (data.medications.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center py-12 text-gray-500">No hay datos para este período</td></tr>';
            return;
        }
        
        tbody.innerHTML = data.medications.map((med, idx) => {
            const trendIcon = med.trend > 0 ? 'fa-arrow-up text-green-600' : med.trend < 0 ? 'fa-arrow-down text-red-600' : 'fa-minus text-gray-400';
            const trendText = med.trend > 0 ? `+${med.trend}%` : med.trend < 0 ? `${med.trend}%` : 'Sin cambios';
            
            return `
                <tr class="border-b hover:bg-gray-50 transition">
                    <td class="py-4 px-4">
                        ${idx === 0 ? '<i class="fa-solid fa-crown text-yellow-500"></i>' : ''}
                        ${idx === 1 ? '<i class="fa-solid fa-medal text-gray-400"></i>' : ''}
                        ${idx === 2 ? '<i class="fa-solid fa-medal text-orange-600"></i>' : ''}
                        ${idx > 2 ? idx + 1 : ''}
                    </td>
                    <td class="py-4 px-4 font-semibold text-gray-900">${med.medication}</td>
                    <td class="py-4 px-4 text-center">
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full font-bold">${med.count}</span>
                    </td>
                    <td class="py-4 px-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <div class="w-32 bg-gray-200 rounded-full h-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: ${med.percentage}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-600">${med.percentage.toFixed(1)}%</span>
                        </div>
                    </td>
                    <td class="py-4 px-4">
                        <span class="text-sm">
                            <i class="fa-solid ${trendIcon} mr-1"></i>
                            ${trendText}
                        </span>
                    </td>
                </tr>
            `;
        }).join('');
        
    } catch (e) {
        console.error('Error loading medication stats:', e);
    }
}

function exportMedicationReport() {
    const period = document.getElementById('period').value;
    const doctor = document.getElementById('doctor_filter').value;
    window.open(`api/export_medication_report.php?period=${period}&doctor=${doctor}`, '_blank');
}

loadDoctors();
loadMedicationStats();
</script>

<?php include 'includes/footer.php'; ?>
